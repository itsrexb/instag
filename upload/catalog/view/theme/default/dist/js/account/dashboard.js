(function () {angular
	.module('instag-app', ['ngTouch']);})(); (function () {function checkoutResponse(json) {
  angular.element(document.getElementById('billing')).scope().checkoutResponse(json);
}

function updatePaymentMethodResponse(json) {
  angular.element(document.getElementById('billing')).scope().updatePaymentMethodResponse(json);
}

// jQuery Functions
//  It Centers the content for X and Y axis - "relative" Arg = reference element to center
jQuery.fn.center = function (relative) {
  var relative = relative || this.parent();
  this.css("position","absolute");
  this.css("top", Math.max(0, (($(relative).outerHeight() - $(this).outerHeight()) / 2) + $(relative).scrollTop()) + "px");
  this.css("left", Math.max(0, (($(relative).width() - $(this).width()) / 2) + $(relative).scrollLeft()) + "px");
  return this;
}})(); (function () {angular
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
}})(); (function () {angular
  .module('instag-app')
  .controller('BillingTabController',BillingTabController);

// Controller services injection
BillingTabController.$inject = ['checkStatusService','timeAgoService','$scope','$timeout','$http','$compile','$rootScope'];

function BillingTabController (checkStatusService,timeAgoService,$scope,$timeout,$http,$compile,$rootScope) {
  var vm = this;
  vm.updateSpeed                     = updateSpeed;
  vm.updateBilling                   = updateBilling;
  vm.updateCart                      = updateCart;
  vm.updateOrderTotals               = updateOrderTotals;
  vm.displayCoupon                   = displayCoupon;
  vm.applyCoupon                     = applyCoupon;
  window.paymentAttemptsFlag         = 0;
  vm.validatePayment                 = validatePayment;
  vm.validateform                    = validateform;
  vm.checkoutResponse                = checkoutResponse;
  $scope.checkoutResponse            = vm.checkoutResponse;
  vm.updatePaymentMethodResponse     = updatePaymentMethodResponse
  $scope.updatePaymentMethodResponse = vm.updatePaymentMethodResponse;
  vm.updatePayment                   = updatePayment;
  vm.cancelAccount                   = cancelAccount;
  // Services  
  vm.checkStatus                     = checkStatusService.checkStatus;
  vm.historyMarkup                   = timeAgoService.historyMarkup;
  // Events broadcast
  vm.uncollapseBilling               = uncollapseBilling;
  vm.collapse                        = collapse;
  // Flags
  vm.updateSpeedFlag                 = false,
  vm.processingPayment               = false;
  vm.updatingCart                    = false;

  /* updateSpeed() - Updates speed information on speed card */
  function updateSpeed($event) {
    if (!vm.updateSpeedFlag && !vm.processingPayment) {
      // Set updating speed to not let customer change plan while it's changing
      vm.updateSpeedFlag = true;
      var clickedElement = angular.element($event.currentTarget);
      // Remove current selected speed
      clickedElement.parent().find('.selected-speed').removeClass('selected-speed');
      // Set clicked element as selected speed
      clickedElement.addClass('selected-speed');
      // Save category
      var category = clickedElement.attr('data-category-id');
      // Show container with products of selected category
      var productsContainer = angular.element(document.getElementById('products'));
      productsContainer.find('.selected-category').removeClass('selected-category');
      productsContainer.find('#category-'+category).addClass('selected-category');
      // Check for a selected speed, set ludicrous as default if not
      if (productsContainer.find('.selected-product').length) {
        var reference = productsContainer.find('.selected-product').attr('data-product-reference');
        var emulateEvent = {
          currentTarget : productsContainer.find('#category-'+category+' .reference-'+reference)
        };
        vm.updateBilling(emulateEvent);
      } else {
        // Release updating speed
        vm.updateSpeedFlag = false;
      };
    };
  }

  /* updateBilling() - Updates product information on billing card */
  function updateBilling($event) {
    if (!vm.updatingCart && !vm.processingPayment) {
      // Set updating cart to not let customer change plan
      vm.updatingCart = true;
      var clickedElement = angular.element($event.currentTarget);
      // Set clicked element as selected product
      clickedElement.parent().parent().find('.selected-product').removeClass('selected-product');
      clickedElement.addClass('selected-product');
      // Check for a selected speed, set ludicrous as default if not
      var speedContainer = angular.element(document.getElementById('speeds'));
      if (!speedContainer.find('.selected-speed').length) {
        speedContainer.children().eq(0).addClass('selected-speed');
        var emulateEvent = {currentTarget:speedContainer.children().eq(0)};
        vm.updateBilling(emulateEvent);
      };
      vm.updateCart();
    };
  }

  /* updateCart() - Updates card information on cart card */
  function updateCart() {
    if (!vm.processingPayment) {
      // Disable confirm and update buttons
      var buttonConfirm = angular.element(document.getElementById('button-confirm'));
      buttonConfirm.attr('disabled','true');
      var buttonUpdate = angular.element(document.getElementById('button-update'));
      buttonUpdate.attr('disabled','true');
      // Set product and account id to call updateCart
      var accountId = angular.element(document.getElementById('left-sidebar')).find('.current-account').attr('data-id');
      var productId = angular.element(document.getElementById('products')).find('.selected-product').attr('data-product-id');
      var code = angular.element(document.getElementById('input-coupon')).val();
      // Set product id and account id as post data
      var post_data = {
        account_id: accountId,
        product_id: productId,
        coupon: encodeURIComponent(code)
      }
      // Make the request
      $http.post('index.php?route=account/billing/cart',post_data).success(function(data){
        if (data.redirect) {
          window.location = data.redirect;
        };

        var payment_card = angular.element(document.getElementById('payment-card'));

        // Release updating cart and speed
        vm.updatingCart = false;
        vm.updateSpeedFlag = false;
        if (data.order_totals) {
          if (data.order_totals.length > 0) {
            payment_card.find('.confirm-payment').removeClass('hidden');
            payment_card.find('.update-payment').addClass('hidden');
          } else {
            payment_card.find('.confirm-payment').addClass('hidden');
            payment_card.find('.update-payment').removeClass('hidden');
          }

          updateOrderTotals(data);
          // Enable coupon input again
          angular.element(document.getElementById('input-coupon')).attr('disabled','false');
          // Remove disabled attribute to confirm and update buttons
          buttonConfirm.removeAttr('disabled').html(buttonConfirm.attr('data-reset-text'));
          buttonUpdate.removeAttr('disabled').html(buttonUpdate.attr('data-reset-text'));
          // Enable coupon input
          angular.element(document.getElementById('input-coupon')).removeAttr('disabled');
          angular.element(document.getElementById('button-coupon')).removeAttr('disabled');
        }
      });
    }
  }

  /* updateOrderTotals() - Updates totals information on cart card */
  function updateOrderTotals(data) {
    if (!vm.processingPayment) {
      // Set the totals markup to push it into the table
      var dataTotals = '';
      dataTotals += '<tbody>';
      for (var i=0; i<data.order_totals.length;i++) {
        if (data.order_totals[i].code == 'total') {
          dataTotals += '<tr><td class="text-right tooltip-container"><strong><span>'+data.order_totals[i].title+'</span></strong></td>';
          dataTotals += '<td class="text-right"><strong>'+data.order_totals[i].text+'</strong></td></tr>';
        } else if (data.order_totals[i].code == 'coupon') {
          var coupon = angular.element(document.getElementById('input-coupon')).val();
          dataTotals += '<tr><td class="text-right tooltip-container" id="coupon-total" data-coupon="'+coupon+'"><span>'+data.order_totals[i].title+'</span></td>';
          dataTotals += '<td class="text-right">'+data.order_totals[i].text+'</td></tr>';
        } else {
          dataTotals += '<tr><td class="text-right tooltip-container"><span>'+data.order_totals[i].title+'</span></td>';
          dataTotals += '<td class="text-right">'+data.order_totals[i].text+'</td></tr>';
        };
      };
      dataTotals += '</tbody>';
      // Append totals markup
      angular.element(document.getElementById('order-totals')).find('.table').empty().html(dataTotals);
    }
  }

  function displayCoupon($event) {
    var clickedElement = angular.element($event.currentTarget);
    clickedElement.addClass('hide');
    clickedElement.parent().find('.form-group').addClass('displayed');
  };

  function applyCoupon() {
    var code = angular.element(document.getElementById('input-coupon')).val();
    var post_data = {
      coupon: encodeURIComponent(code)
    }
    $http.post('index.php?route=total/coupon/coupon',post_data).success(function(data){
      if (data.redirect) {
        window.location = data.redirect;
      };
      if (data.success) {
        vm.updateCart();
      } else {
        if (data.error) {
          var alert = '<div class="alert alert-danger">'+data.error+'</div>';
          angular.element(document.getElementById('modules')).append(alert);
        }
      }
    });
  }

  /* updateOrderTotals() - Validates payment */
  function validatePayment($event) {
    vm.processingPayment = true;
    // Clicked Element
    var clickedElement = angular.element($event.currentTarget),
        productId      = angular.element(document.getElementById('products')).find('.selected-product').attr('data-product-id'),
        couponCode     = angular.element(document.getElementById('coupon-total')).attr('data-coupon'),
        post_data      = {
                          account_id: angular.element(document.getElementById('left-sidebar')).find('.current-account').attr('data-id'),
                          product_id: productId,
                          coupon : couponCode
                        },
        buttonConfirm  = angular.element(document.getElementById('button-confirm')),
        buttonUpdate   = angular.element(document.getElementById('button-update'));
    buttonConfirm.button('loading');
    buttonUpdate.button('loading');
    if (window.paymentAttemptsFlag < 3) {
      $http.post('index.php?route=account/billing/validate',post_data).success(function(data){
        if (data.redirect) {
          window.location = data.redirect;
        };
        if (data.success) {
          angular.element(document.getElementById('payment')).attr('action', angular.element(document.getElementById('payment')).attr('data-' + data.action));
          $timeout(function(){
            angular.element(document.getElementById('payment')).find('input[type="submit"]').trigger('click');
          },0);
        } else if (data.errors) {
          angular.element(document.getElementById('billing-notifications')).html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + data.errors.empty + '</div>');
          buttonConfirm.button('reset');
          buttonUpdate.button('reset');
          window.paymentAttemptsFlag++;
        }
      });
    } else {
      angular.element(document.getElementById('billing-modal')).addClass('displayed');
    }
  }

  /*
    validateForm() - Loops through all input fields
    Function arguments:
    formId = form's id
  */
  function validateform (formId) {
    var value = true;
    var form = angular.element(document.getElementById(formId));
    var fields = form.find('input[type="text"]','textarea');
    for (var i=0;i<fields.length;i++) {
      if (fields.eq(i).val() == '') {
        value = false;
      }
    }
    return value;
  };

  function checkoutResponse(data) {
    if (data.success) {
      var accountId = angular.element(document.getElementById('left-sidebar')).find('.current-account').attr('data-id');
      $http.get('index.php?route=account/billing/success&account_id='+accountId).success(function(data){
        if (data.redirect) {
          window.location = data.redirect;
        };
        if (data.success) {
          var alert = '<div class="alert alert-success"><i class="fa fa-check-circle"></i> '+data.success+'</div>'
        };
        // Track conversions
        for (var i = 0, conversion; conversion = data.conversions[i]; ++i) {
            alert += conversion;
        };
        // Append alert to dashboard
        angular.element(document.getElementById('billing-notifications')).html(alert);
        // remove order totals
        angular.element(document.getElementById('order-totals')).find('.table').empty().html('');
        // Update payments card
        var dataCard = '';
        if (data.upcoming_orders) {
          for (var i = 0, upcomingOrder; upcomingOrder = data.upcoming_orders[i]; ++i) {
            dataCard += '<tr><td class="text-left">';
            for (var j = 0, upcomingOrderProduct; upcomingOrderProduct = upcomingOrder.products[j]; ++j) {
              dataCard += '<strong>'+upcomingOrderProduct.name+'</strong>';
            };
            dataCard += '<br>'+upcomingOrder.date+'</td>';
            dataCard += '</td>';
            dataCard += '<td class="text-right"><strong>'+upcomingOrder.total+'</strong><br>&nbsp;</td>';
            dataCard += '</tr>';
          };
          angular.element(document.getElementById('card-payments')).removeClass('hide');
          angular.element(document.getElementById('payments')).find('.table').empty().append(dataCard);
        };
        // Update capabilities
        var followSpeeds     = angular.element(document.getElementById('follow-range')).find('.card-input .speed-span'),
            unfollowSpeeds   = angular.element(document.getElementById('unfollow-range')).find('.card-input .speed-span'),
            newFollowSpeed   = 0,
            newUnfollowSpeed = 0;
        // Loop through all follow speeds and check if there is a new one avaiable
        for (var i=0; i<followSpeeds.length; ++i) {
          var followSpeed = angular.element(followSpeeds[i])
          if (data.capabilities.indexOf('speed_' +angular.element(followSpeed).attr('data-speed')) > -1) {
            followSpeed.data('valid', '1').attr('data-valid', '1');
            if (followSpeed.data('speed-value') > newFollowSpeed) {
                newFollowSpeed = parseInt(followSpeed.data('speed-value'));
                if (newFollowSpeed == 3) {
                  angular.element(document.getElementById('follow-range')).removeClass('upgrade');
                  angular.element(document.getElementById('unfollow-range')).removeClass('upgrade');
                };
            };
          } else {
            followSpeed.data('valid', '0').attr('data-valid', '0');
            angular.element(document.getElementById('follow-range')).addClass('upgrade');
            angular.element(document.getElementById('unfollow-range')).addClass('upgrade');
          };
        };
        // Loop through all unfollow speeds and check if there is a new one avaiable
        for (var i=0; i<unfollowSpeeds.length; ++i) {
          var unfollowSpeed = angular.element(unfollowSpeeds[i])
          if (data.capabilities.indexOf('speed_' + unfollowSpeed.data('speed')) > -1) {
            unfollowSpeed.data('valid', '1').attr('data-valid', '1');
            if (unfollowSpeed.data('speed-value') > newUnfollowSpeed) {
              newUnfollowSpeed = parseInt(unfollowSpeed.data('speed-value'));
            };
          } else {
            unfollowSpeed.data('valid', '0').attr('data-valid', '0');
          };
        };
        // Check for expired or disconnected accounts
        var contentMsg = angular.element(document.getElementById('content')).attr('data-msg');
        if (contentMsg == 'expired' || contentMsg == 'invalid_token') {
          var accountElement = angular.element(document.getElementById('left-sidebar')).find('.current-account'),
              accountId      = accountElement.attr('data-id'),
              buttonAccount  = angular.element(document.getElementById('change-status-btn'));
          $http.get('index.php?route=account/account/start&account_id=' + accountId).success(function(data){
            if (data.redirect) {
              window.location = data.redirect;
            };
            if (data.success) {
                  // Left-sidebar change status button
              var squareStatus   = accountElement.find('.account-square-status'),
                  accountStatus  = accountElement.find('.account-status'),
                  // Set a list of classes to clear on elements
                  removeClasses  = 'reconnect kickoff stopped started start stop disabled expired loading';
              if (accountElement.hasClass('current-account')) {
                // Update right-sidebar elements
                buttonAccount.removeClass(removeClasses).addClass('started').html('Stop').attr('data-action','stop');
                buttonAccount.parent().find('i').removeClass('fa-stopped').addClass('fa-started');
                buttonAccount.parent().find('.item-content p').html('Started');
              };
              // Change left-sidebar button
              squareStatus.removeClass(removeClasses).addClass('started').attr('data-action','started');
              accountStatus.removeClass(removeClasses).addClass('started');
              // Check for mobile and change status
              if (window.innerWidth < 1200) {
                var headerStatus = angular.element(document.getElementById('logo-u-container')).find('.account-item .account-status');
                headerStatus.removeClass(removeClasses).addClass('started');
              }
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
                // Update right-sidebar events
                var historyContent = angular.element(document.getElementById('history-content'));
                if (data.event) {
                  var item = vm.historyMarkup(data.event,true);
                  historyContent.prepend(item);
                }
              }
            }
            angular.element(document.getElementById('loader')).removeClass('loading');
          });
        };
        // Update input range
        angular.element(document.getElementById('follow-range')).find('.input-range').val(newFollowSpeed).triggerHandler('change');
        angular.element(document.getElementById('unfollow-range')).find('.input-range').val(newUnfollowSpeed).triggerHandler('change');
        // Clear Ludicrous upgrade card on right-sidebar
        if (data.capabilities.length == 3) {
          angular.element(document.getElementById('upgrade-ludicrous')).remove();
        }
        // Clear Account trial on right-sidebar
        if (angular.element(document.getElementById('account-trial')).length) {
          angular.element(document.getElementById('account-trial')).remove();
        }
        // Update left and right sidebar
        vm.checkStatus('');
        if (angular.element(document.getElementById('content')).attr('data-msg') == 'expired') {
          angular.element(document.getElementById('account')).find('.run-status .item-content p').html(data.account_status);
          angular.element(document.getElementById('change-status-btn')).attr('data-action','start').html(data.button_start).removeClass('expired-button');
        }
        // Billing cards collapse for mobile
        if (window.innerWidth < 730) {
          // Uncollapse speed and billing cards
          angular.element(document.getElementById('billing')).find('.collapse').removeClass('opened');
          // Update Speed header
          var speedCollapse = angular.element(document.getElementById('billing')).find('.collapse').eq(0),
          speedHeader = speedCollapse.find('.collapse-header'),
          selectedSpeed = speedCollapse.find('.selected-speed'),
          selectedProduct = angular.element(document.getElementById('billing')).find('.collapse').eq(1).find('.selected-product');
          speedHeader.addClass('has-speed').attr('data-selected-product',selectedProduct.attr('data-product-id'));
          speedHeader.find('.selected-speed-container').attr('id','speed-selected-'+selectedProduct.attr('data-product-id'));
          speedHeader.find('.selected-speed-container').find('span').empty().html(selectedSpeed.find('h5').html());
          // Update Billing header
          var billingCollapse = angular.element(document.getElementById('billing')).find('.collapse').eq(1),
          billingHeader = billingCollapse.find('.collapse-header'),
          selectedBilling = billingCollapse.find('.selected-product');
          billingHeader.addClass('has-billing').attr('data-selected-product',selectedProduct.attr('data-product-id'));
          billingHeader.find('.selected-billing-container').attr('billing-selected-'+'id',selectedProduct.attr('data-product-id'));
          billingHeader.find('.selected-billing-container').find('span').empty().html(selectedBilling.find('h5').html());
        }
        // Update Left and Right Sidebar status buttons for expired accounts
        if (angular.element(document.getElementById('content')).attr('data-msg') == 'expired') {
          vm.checkStatus('');
          // Update left sidebar Tooltip
          angular.element(document.getElementById('left-sidebar')).find('.current-account .account-square-status').attr('data-tooltip',data.tooltip_stopped);
          angular.element(document.getElementById('account')).find('.account-status-icon').attr('data-tooltip',data.tooltip_stopped);
        }

        angular.element(document.getElementById('payment-card')).find('.confirm-payment').addClass('hidden');
        angular.element(document.getElementById('payment-card')).find('.update-payment').removeClass('hidden');

        angular.element(document.getElementById('button-confirm')).button('reset');
        angular.element(document.getElementById('button-update')).button('reset');

        vm.processingPayment = false;
      });
    } else {
      if (data.error) {
        // Append alert to dashboard
        var alert = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i>'+data.error+'</div>'
        angular.element(document.getElementById('billing-notifications')).html(alert);
      }
      // Reset billing process
      angular.element(document.getElementById('button-confirm')).button('reset');
      angular.element(document.getElementById('button-update')).button('reset');
      vm.processingPayment = false;
    }
  }

  function updatePaymentMethodResponse(data) {
    if (data.success) {
      angular.element(document.getElementById('billing-notifications')).html('<div class="alert alert-success"><i class="fa fa-check"></i>' + data.success + '</div>');
    } else if (data.error) {
      angular.element(document.getElementById('billing-notifications')).html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i>'+data.error+'</div>');
    }
    vm.processingPayment = false;

    angular.element(document.getElementById('button-confirm')).button('reset');
    angular.element(document.getElementById('button-update')).button('reset');
  }

  /* updatePayment() - Validates payment */
  function updatePayment($event) {
    // Update select dropdown
    angular.element(document.getElementById('payment-method-token')).val(angular.element($event.currentTarget).attr('data-value')).triggerHandler('change');
  }

  /* cancelAccount() - Cancels current account */
  function cancelAccount(accountId) {
    var post_data = { account_id: accountId },
        button    = angular.element(document.getElementById('button-cancel-account'));
    button.html(button.attr('data-loading')+'...');
    button.attr('disabled','true');
    $http.post('index.php?route=account/account/delete',post_data).success(function(data){
      if (data.redirect) {
        window.location = data.redirect;
      } else {
        if (data.success) {
          button.html(button.attr('data-yes'));
          button.removeAttr('disabled')
        }
      }
      /*
      */
    });
  }

  // Events Listeners
  $rootScope.$on('updateSpeed',function(e,$event){
    vm.updateSpeed($event);
  });

  $rootScope.$on('updateBilling',function(e,$event){
    vm.updateBilling($event);
  });

  // Events broadcast
  function uncollapseBilling() {
    // Event Listener: TabsController
    $rootScope.$broadcast('uncollapseBilling');
  }

  function collapse() {
    // Event Listener: TabsController
    $rootScope.$broadcast('collapse');
  }

}})(); (function () {angular
  .module('instag-app')
	.controller('ChartController',ChartController);

// Controller services injection
ChartController.$inject = ['chartService','$http','$scope','$rootScope'];

function ChartController(chartService,$http,$scope,$rootScope) {
	var vm          = this;
	vm.getChart     = getChart;
  // Services
  vm.showChart    = chartService.showChart;
  vm.changeHeight = chartService.changeHeight;

	function getChart() {
		$http.post('index.php?route=account/report_follower_growth',post_data).success(function(){
      if (data.redirect) {
        window.location = data.redirect;
      }
      if (data.success) {
		    // Draw Chart for dashboard tab
		    vm.showChart();
		    // Match height of Chart and Influence cards
		    vm.changeHeight();
      }
		});
	}

  // Events Listeners
  $rootScope.$on('getChart',function(e){
    vm.getChart();
  });

};})(); (function () {angular
  .module('instag-app')
  .controller('DashboardTabController',DashboardTabController);

// Controller services injection
DashboardTabController.$inject = ['chartService','$scope','$timeout','$http','$compile'];

function DashboardTabController (chartService,$scope,$timeout,$http,$compile) {
  var vm = this;
  vm.changeChart  = changeChart;
  vm.changeHeight = changeHeight;
  vm.showChart    = showChart;
  var slidingFlag;


	/* changeChart() = Updates Chart Information */
  function changeChart($event) {
      var clickedElement = angular.element($event.currentTarget);
      var itemsContainer = angular.element(document.getElementById('chart-carousel')).find('.carousel-inner');
      var activeElement = itemsContainer.find('.active');
      if (!slidingFlag) {
          slidingFlag = true;
          if (clickedElement.data('slide') == 'next') {
              if (activeElement.index() < itemsContainer.length) {
                  var next = itemsContainer.children().eq(activeElement.index()+1);
              } else {
                  var next = itemsContainer.children().eq(0);
              }
              getChartData(next);
          } else {
              if (activeElement.index() > 0) {
                  var next = itemsContainer.children().eq(activeElement.index()-1);
              } else {
                  var next = itemsContainer.children().eq(itemsContainer.length);
              }
              getChartData(next);
          }
          setTimeout(function () {
              slidingFlag = false;
          },600);
      }
      $('.carousel').carousel({
          pause: true,
          interval: false
      });
  }

  // Get services functions
  function changeHeight() {
    chartService.changeHeight();
  }
  
  function showChart() {
    chartService.showChart();
  }

}})(); (function () {angular
  .module('instag-app')
  .controller('HeaderController',HeaderController);

// Controller services injection
HeaderController.$inject = ['$scope','$timeout','$http','$compile','$rootScope'];

function HeaderController ($scope,$timeout,$http,$compile,$rootScope) {
  var vm = this;
  vm.openMenu         = openMenu;
  vm.closeMenu        = closeMenu;
  vm.openLeftSidebar  = openLeftSidebar;
  // Events Broadcast
  vm.closeLeftSidebar = closeLeftSidebar;

	/*
	  openMenu() = It opens and closes menu dropdown 
	  Function parameters:
	  mobile = tells the function that screen is in mobile view
	*/
  function openMenu(mobile) {
    var mobile = mobile || false;
    var dropdown = angular.element(document.getElementById('tab-dropdown-middle'));
    // Check if clicked element is in mobile view or tablet view
    if (window.innerWidth > 730 || mobile) {
      // Check if it's open or not
      if (dropdown.find('ul').hasClass('displayed')) {
        dropdown.find('ul').removeClass('displayed');
        dropdown.find('ul').addClass('hide');
      } else {
        dropdown.find('ul').addClass('displayed');
        dropdown.find('ul').removeClass('hide');
      }
    }
  }

  /* closeMenu() = It closes menu dropdown */
  function closeMenu() {
    var dropdown = angular.element(document.getElementById('tab-dropdown-middle'));
    if (dropdown.find('ul').hasClass('displayed')) {
      dropdown.find('ul').removeClass('displayed');
      dropdown.find('ul').addClass('hide');
    }
  }

  /*
    openLeftSidebar() - Opens leftsidebar
    Function parameters:
    notMobile: it tells if view it's on mobile or not
  */
  function openLeftSidebar(notMobile) {
    var notMobile   = notMobile || false,
        leftSidebar = angular.element(document.getElementById('left-sidebar'));
    // Check if window is mobile
    if (!leftSidebar.hasClass('open-sidebar')) {
      if (notMobile && window.innerWidth <= 1570 && window.innerWidth > 1200) {
        if (window.innerWidth <= 1570)
          leftSidebar.addClass('open-sidebar');
      } else if (!notMobile && window.innerWidth <= 1200) {
        leftSidebar.addClass('open-sidebar');
        angular.element(document.getElementById('close-sidebar')).addClass('opened-close-sidebar');
      }
    } else {
      leftSidebar.removeClass('open-sidebar');
    }
  }

  // Events broadcast
  function closeLeftSidebar() {
    // Listener: LeftSidebarController
    $rootScope.$broadcast('closeLeftSidebar');
  }

}})(); (function () {angular
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
}})(); (function () {angular
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

}})(); (function () {angular
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

}})(); (function () {angular
  .module('instag-app')
  .controller('LocationsController',LocationsController);

// Controller services injection
LocationsController.$inject = ['$scope','$timeout','$http','$compile','$rootScope'];

function LocationsController ($scope,$timeout,$http,$compile,$rootScope) {
	
}})(); (function () {angular
  .module('instag-app')
  .controller('ModalsController',ModalsController);

// Controller services injection
ModalsController.$inject = ['modalsService','timeAgoService','$scope','$timeout','$http','$compile','$rootScope'];

function ModalsController (modalsService,timeAgoService,$scope,$timeout,$http,$compile,$rootScope) {
  var vm = this;
  vm.closeModal          = closeModal;
  vm.addAccount          = addAccount;
  vm.reconnectAccount    = reconnectAccount;
  vm.helpModal           = helpModal;
  vm.videoModal          = videoModal;
  vm.cancelAction        = cancelAction;
  vm.topSourcesWarning   = topSourcesWarning;
  // Services
  vm.displayWarning      = modalsService.displayWarning;
  vm.historyMarkup       = timeAgoService.historyMarkup;
  // Events broadcast
  vm.changeAccountStatus = changeAccountStatus;
  vm.displayTooltip      = displayTooltip;
  vm.hideTooltip         = hideTooltip;
  vm.triggerHideTooltip  = triggerHideTooltip;
  vm.closeLeftSidebar    = closeLeftSidebar;

  /* closeModal() - Closes modal called by $event.currentTarget */
  function closeModal($event) {
    var $event = $event || false;
    if ($event) {
      var clickedElement = angular.element($event.target);
      if (angular.element($event.target).hasClass('modal')) {
        clickedElement.removeClass('displayed');
        if ($event.target.id == 'video-modal') {
          $timeout(function(){
            angular.element(document.getElementById('stop-video')).trigger('click');
          });
        }
        vm.triggerHideTooltip();
      }
    } else {
      angular.element(document.getElementById("dashboard")).find('.modal.displayed').removeClass('displayed');
    }
  }
  
  /* addAccount() - Check's for form input fields, validates them and add a new user if data is correct */
  function addAccount () {
    // Check for empty username
    var username = angular.element(document.getElementById('add-account-username')).val();
    if (username == '') {
      $timeout(function() {
        angular.element(document.getElementById('username-empty')).trigger('click');
      },0);
    } else {
      // Check for empty password
      var password = angular.element(document.getElementById('add-account-password')).val();
      if (password == '') {
        $timeout(function() {
          angular.element(document.getElementById('password-empty')).trigger('click');
        },0);
      } else {
        // Hide any tooltips
        vm.triggerHideTooltip();
        // Disable button
        var clickedElement = angular.element(document.getElementById('button-add-instagram'));
        clickedElement.addClass('loading-button').attr('disabled','true');
        // Make the request
        var post_data = {
          username: username,
          password: password
        };
        $http.post('index.php?route=account/instagram/insert',post_data).success(function(data){
          // Check for redirections
          if (data.redirect) {
            window.location = data.redirect;
          }
          // Check for success on response
          if (data.success) {
            // Clone current account and set the new one
            var newAccount =  angular.element(document.getElementById('left-sidebar')).find('.instagram-accounts .current-account').clone();
            newAccount.removeClass('hide');
            newAccount.attr("ng-click","leftSidebar.setCurrent($event)");
            newAccount.attr('data-false','true');
            newAccount.attr('data-id',data.account_id).removeClass('current-account').addClass('new-account');
            newAccount.find('.account-img .account-status').removeClass('started reconnect').addClass('kickoff');
            newAccount.find('.account-img img').remove();
            newAccount.find('.account-img').append('<img src="'+data.profile_picture+'">');
            // Check for username char length
            if (username.length > 14) {
              username = username.substring(0,14)+'...';
            }
            newAccount.find('.sidebar-item-title span').attr('data-username',username).html(username+'<br /><small>Account</small>');
            newAccount.find('.account-square-status').attr('id',data.account_id).attr('data-action','kickoff').attr('data-tooltip',data.kickoff_tooltip)
            newAccount.find('.account-square-status').removeClass('started reconnect').addClass('kickoff');
            newAccount.find('.account-square-status').attr('ng-click','leftSidebar.changeAccountStatus("'+data.account_id+'",$event)');
            newAccount.removeClass('hide');
            // Append the new account to accounts container
            var accountsContainer = angular.element(document.getElementById('left-sidebar')).find('.instagram-accounts'),
                scope             = accountsContainer.scope();
            accountsContainer.append($compile(newAccount)(scope));
            // Hide add-account modal
            angular.element(document.getElementById('modal-add-instagram')).removeClass('displayed');
            // Set new account has current
            $timeout(function() {
              newAccount.trigger('click');
            },0);
          } else {
            // Set tooltip content according 
            var errorContainer = angular.element(document.getElementById('add-instagram-error'));
            var emulatedEvent = { currentTarget: document.getElementById('add-instagram-error') };
            if (data.errors.warning) {
              errorContainer.attr('data-tooltip',data.errors.warning);
            } else if (data.errors.exists) {
              errorContainer.attr('data-tooltip',data.errors.exists);
            } else {
              errorContainer.attr('data-tooltip',data.errors);
            }
            vm.displayTooltip(emulatedEvent)
          }
          // Enable button
          clickedElement.removeAttr('disabled');
          angular.element(document.getElementById('button-add-instagram')).removeClass('loading-button');
        });
      }
    }
  }

  /*
    reconnectAccount() - Reconnects a disconnected account
    Function parameters:
    $event: caller element
    accountId: selected account ID
  */
  function reconnectAccount($event,accountId) {
    // Disable reconnect button
    var clickedElement = angular.element($event.currentTarget);
    clickedElement.attr('disabled','true');
    // Get modal element
    var modal = angular.element(document.getElementById('reconnect-modal'));
    // Check for username empty value
    var username = modal.find('#instagram_username').val();
    if (username == '') {
      $timeout(function() {
        modal.find('.username-tooltip').trigger('click');
      },0);
    } else {
      // Check for password empty value
      var password = modal.find('#instagram_password').val();
      if (password == '') {
        $timeout(function() {
          modal.find('.password-tooltip').trigger('click');
        },0);
      } else {
        // Display button as loading
        angular.element(document.getElementById('button-reconnect')).addClass('loading-button');
        // Set post data
        var post_data = {
          username: username,
          password: password
        }
        // Make the request
        $http.post('index.php?route=account/instagram/reconnect&account_id='+accountId,post_data).success(function(data){
          // Check for redirections
          if (data.redirect) {
            window.location = data.redirect;
          };
          // Check for success
          if (data.success) {
            // Update left-sidebar icons and buttons
            var currentAccount = angular.element(document.getElementById('left-sidebar')).find('.current-account');
            currentAccount.removeAttr('data-false');
            currentAccount.find('.account-status').removeClass('reconnect').addClass('stopped');
            currentAccount.find('.account-square-status').removeClass('reconnect').addClass('stopped');
            // Update right-sidebar icons and buttons
            var accountStatus      = angular.element(document.getElementById('account')).find('.run-status'),
                rightSidebarButton = angular.element(document.getElementById('change-status-btn'));
            accountStatus.find('i').removeClass('fa-chain-broken fa-reconnect').addClass('fa-stopped');
            accountStatus.find('.item-content p').html('Stopped');
            rightSidebarButton.removeClass('reconnect-button').addClass('stopped').attr('data-action','start').html('Start');
            // Change tooltips
            if (data.tooltip) {
              currentAccount.find('.account-square-status').attr('data-tooltip',data.tooltip);
              angular.element(document.getElementById('account')).find('.account-status .account-status-icon').attr('data-tooltip',data.tooltip)
            } else {
              currentAccount.find('.account-square-status').attr('data-tooltip','');
              angular.element(document.getElementById('account')).find('.account-status .account-status-icon').attr('data-tooltip')
            }
            // Clear button loader
            angular.element(document.getElementById('button-reconnect')).removeClass('loading-button');
            // Remove data-msg
            angular.element(document.getElementById('content')).attr('data-msg','');
            // Add event to right-sidebar
            var historyContent = angular.element(document.getElementById('history-content'));
            if (data.event) {
              var item = vm.historyMarkup(data.event,true);
              historyContent.prepend(item);
            }
            // Hide modal
            modal.removeClass('displayed');
            // Restore sidebar and modal indexes
            angular.element(document.getElementById('right-sidebar')).css('z-index','4');
            angular.element(document.getElementById('reconnect-modal')).css('z-index','4');
            // Start account again
            vm.changeAccountStatus(event,accountId)
          } else if (data.errors) {
            if (data.errors.warning) {
              modal.find('.username-tooltip').attr('data-tooltip',data.errors.warning);
              $timeout(function() {
                modal.find('.username-tooltip').trigger('click');
              },0);
              angular.element(document.getElementById('button-reconnect')).removeClass('loading-button');
            } else {
              modal.find('.username-tooltip').attr('data-tooltip',data.errors);
              $timeout(function() {
                modal.find('.username-tooltip').trigger('click');
              },0);
              angular.element(document.getElementById('button-reconnect')).removeClass('loading-button');
            }
          }
          // Enable reconnect button
          clickedElement.removeAttr('disabled');
        });
      }
    }
  }

  /* 
    helpModal() - Changes embed video src and calls videoModal()
    Function parameters:
    $event = provides video ID through data-video-id attribute
  */
  function helpModal($event) {
    if (angular.element(window).innerWidth() > 980) {
      var videoContainer =  angular.element(document.getElementById('help-video'));
      // Show Loader
      videoContainer.parent().find('#video-loader').addClass('displayed');
      videoContainer.css('opacity','0');
      var videoId = angular.element($event.currentTarget).attr('data-video-id'),
          videoSrc = 'https://www.youtube.com/embed/'+videoId+'?rel=0&amp;controls=0&amp;showinfo=0';
      vm.videoModal();
      if (videoContainer.attr('src') != videoSrc+"&enablejsapi=1") {
        videoContainer.attr('src',videoSrc+"&enablejsapi=1");
        videoContainer.load(function(){
          videoContainer.parent().find('#video-loader').removeClass('displayed');
          videoContainer.css('opacity','1');
          $timeout(function(){
            videoContainer.parent().find('#help-video-button').trigger('click');
          },0);
        });
      } else {
        videoContainer.parent().find('#video-loader').removeClass('displayed');
        videoContainer.css('opacity','1');
        $timeout(function(){
          videoContainer.parent().find('#help-video-button').trigger('click');
        },0);
      }
    } else {
      var videoId  = angular.element($event.currentTarget).attr('data-video-id'),
          videoSrc = 'https://www.youtube.com/embed/'+videoId;
      window.open(videoSrc, '_blank');
    }
  }

  /* 
    videoModal() - It opens or closes tutorial modal depending on argument
    Function parameters:
    close = tells function to close tutorial modal
  */
  function videoModal(close) {
    var close = close || false;
    angular.element(document.getElementById('video-modal')).addClass('displayed');
    vm.closeLeftSidebar();
    if (close) {
      angular.element(document.getElementById('video-modal')).removeClass('displayed');
    }
  }

  /* cancelAction() - Hides warning modal when user press cancel button */
  function cancelAction(container) {
    angular.element(document.getElementById(container)).removeClass('displayed');
  }

  /*
    topSourcesWarning() - Displays Top Sources Warning
    Function parameters:
    container:  Warning container
    sourceId:   SourceID
    sourceType: Source type
  */
  function topSourcesWarning(container,sourceId,sourceType) {
    var warningElement = angular.element(document.getElementById(container));
    warningElement.find('.confirm').attr('data-source',sourceId).attr('data-type',sourceType);
    warningElement.addClass('displayed');
  }

  // Events Listeners
  $rootScope.$on('closeModal',function(e,$event){
    vm.closeModal($event);
  });

  // Events broadcast
  function changeAccountStatus (event,accountId) {
    // Listener: LeftSidebarController
    $rootScope.$broadcast('changeAccountStatus',event,accountId);
  }

  function displayTooltip($event) {
    // Listener: TooltipsController
    $rootScope.$broadcast('displayTooltip',$event);
  }

  function hideTooltip($event) {
    // Listener: TooltipsController
    $rootScope.$broadcast('hideTooltip',$event);
  }

  function triggerHideTooltip() {
    // Listener: TooltipsController
    $rootScope.$broadcast('triggerHideTooltip');
  }

  function closeLeftSidebar() {
    // Listener: LeftSidebarController
    $rootScope.$broadcast('closeLeftSidebar');
  }

}})(); (function () {angular
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
}})(); (function () {angular
  .module('instag-app')
  .controller('ResizeController',ResizeController);

ResizeController.$inject = ['chartService','$scope','$compile','$timeout','$rootScope']

function ResizeController (chartService,$scope,$compile,$timeout,$rootScope) {
  var vm                    = this;
      vm.responsiveFlag     = false;
      vm.responsive         = responsive;
      vm.mobileResponsive   = mobileResponsive;
      // Services
      vm.changeHeight       = chartService.changeHeight;
      // Events Broadcast
      vm.closeLeftSidebar   = closeLeftSidebar;

  /*
    responsive() - DOM manipulation for mobile view
    Function parameters:
    width: window width
  */
  function responsive (width) {
    var width = width || window.innerWidth();
    // Check for dashboard tab selected before matching 'my influence' and 'my followers' cards
    if (angular.element('tab-header-middle').find('.current-tab').attr('data-tab') == 'dashboard') {
      vm.changeHeight();
    }
    // Check tab-header width
    var tabHeader      = angular.element(document.getElementById('tab-header-middle')),
        leftSidebar    = angular.element(document.getElementById('left-sidebar')),
        rightSidebar   = angular.element(document.getElementById('right-sidebar')),
        availableSpace = angular.element(window).innerWidth() - (leftSidebar.innerWidth() + rightSidebar.innerWidth());

    if ((!window.tabHeaderWidth && (angular.element(window).innerWidth() > 1200)) || (window.tabHeaderWidth <= 40)) {
      window.tabHeaderWidth = 0;

      // Summation of all tab-header elements width
      for (var i = 0; i< tabHeader.children().length; i++) {
        window.tabHeaderWidth += tabHeader.children().eq(i).outerWidth(true);
      }
      // Add Tab Header Padding
      window.tabHeaderWidth += 40;
    }

    if (window.tabHeaderWidth) {
      // Check if tab-header elements are wider tha available space
      if (window.tabHeaderWidth >= availableSpace) {
        if (!angular.element(document.getElementById('content')).hasClass('overwidth')) {
          angular.element(document.getElementById('content')).addClass('overwidth');
        }
      } else {
        if (angular.element(document.getElementById('content')).hasClass('overwidth')) {
          angular.element(document.getElementById('content')).removeClass('overwidth');
        }
      }
    }

    // Tablet Size
    if (width < 1200) {
      // Clear previous account-item on container (account image on header)
      angular.element(document.getElementById('logo-u-container')).find('.account-item').remove();
      // Clone current account
      var currentAccount = angular.element(document.getElementById('left-sidebar')).find('.current-account').clone();
      // Remove angular directives
      currentAccount.removeAttr('ng-click');
      currentAccount.find('.account-square-status').removeAttr('ng-click');
      currentAccount.find('.sidebar-item-title').remove();
      // Append account to logo-container so it can display image on header
      angular.element(document.getElementById('logo-u-container')).append($compile(currentAccount)($scope));
      // If there's no image on account item
      if (currentAccount.find('.account-img img').length < 1) {
        // Show instag "U" logo
        angular.element(document.getElementById('logo-u')).addClass('displayed');
        angular.element(document.getElementById('logo-u-container')).find('.account-status').remove();
      } else {
        // Hide instag "U" logo
        angular.element(document.getElementById('logo-u')).removeClass('displayed');
      }
      // Close left-sidebar
      vm.closeLeftSidebar();
      $timeout(function() {
        // Select current tab
        var content    = angular.element(document.getElementById('content')),
            currentTab = content.find('.tab-header .current-tab').attr('data-tab');
        content.find('.dropdown-menu .'+currentTab).triggerHandler('click');
      },0);
      // Call mobileResponsive()
      mobileResponsive(width);
    } else {
      // Clear header image
      angular.element(document.getElementById('logo-u-container')).find('.account-item').remove();
      // Call mobileResponsive()
      mobileResponsive(width);
    }

    // Take a look at window height
    var windowHeight      = angular.element(window).innerHeight(),
        leftSidebar       = angular.element(document.getElementById('left-sidebar')),
        instaAccounts     = leftSidebar.find('.instagram-accounts'),
        addInstaBtn       = leftSidebar.find('#add-account'),
        bottomLeftSidebar = leftSidebar.find('#bottom-left-sidebar');
    // Get Instagram Accounts Total Height
    if (!vm.instaAccountsHeight) {
      vm.instaAccountsHeight = 0;
      for (var i = 0; i<= instaAccounts.children().length ;i++) {
        vm.instaAccountsHeight += instaAccounts.children().eq(i).innerHeight();
      }
    }
    if (windowHeight <= (vm.instaAccountsHeight + addInstaBtn.outerHeight() + bottomLeftSidebar.outerHeight() + 70)) {
      if (!instaAccounts.hasClass('overflow')) {
        instaAccounts.addClass('overflow');
      }
      instaAccounts.css('maxHeight',(windowHeight - (bottomLeftSidebar.outerHeight() + addInstaBtn.outerHeight() + 70)));
    } else {
      if (instaAccounts.hasClass('overflow')) {
        instaAccounts.removeClass('overflow');
        instaAccounts.css('maxHeight','none');
      }
    }
  }

  /*
    mobileResponsive() - DOM manipulation for mobile view
    Function parameters:
    width: window width
  */
  function mobileResponsive(width) {
    if (width < 730) {
      // Get main content and right sidebar elements
      var content      = angular.element(document.getElementById('content')),
          rightSidebar = angular.element(document.getElementById('right-sidebar'));
      // If it's not kickoff
      if (!content.attr('data-kickoff')) {
        // Check if #account is not included on main content
        if (content.find('#account').length == 0) {
          // Include it
          var tabContent = angular.element(document.getElementById('tab-body-sidebar')).html();
          content.find('.middle-tab').prepend($compile(tabContent)($scope));
        }
        $timeout(function() {
          var content    = angular.element(document.getElementById('content')),
              // Check if current tab it's dashboard  
              currentTab = content.find('.tab-header .current-tab').attr('data-tab');
          if (currentTab == 'dashboard' && !vm.responsiveFlag) {
            vm.responsiveFlag = true;
            // Select "account" tab
            content.find('.dropdown-menu .account').triggerHandler('click');
          }
        },0);
        // Update chart options
        if (window.graphic) {
          if (window.graphic.options.maintainAspectRatio) {
            window.graphic.options.maintainAspectRatio = false;
            window.graphic.update();
          }
        }
      // If account it's on kickoff
      } else {
        // Check if start-account tab is not included on main content
        if (content.find('#start-account').length == 0) {
          // Include it
          var tabContent = angular.element(document.getElementById('tab-body-sidebar')).html();
          content.find('.middle-tab').prepend($compile(tabContent)($scope));
          content.find('#start-account').removeClass('current-account');
          // Empty Right sidebar
          angular.element(document.getElementById('tab-body-sidebar')).empty();
        }
        vm.responsiveFlag = false;
        $timeout(function() {
          var content = angular.element(document.getElementById('content'));
          // Check if current tab it's dashboard  
          var currentTab = content.find('.tab-header .current-tab').attr('data-tab');
          if (currentTab == 'kickoff') {
            // Select "account" tab
            content.find('.dropdown-menu .start-account').triggerHandler('click');
          }
        },0);
      }
    } else if (width > 730) {
      // Remove hide class from menu-dropdown
      angular.element(document.getElementById('tab-dropdown-middle')).removeClass('hide');
      // Get main content element
      var content = angular.element(document.getElementById('content'));
      // If it's not kickoff
      if (!content.attr('data-kickoff')) {
        $timeout(function() {
          var content = angular.element(document.getElementById('content'));
          var currentTab = content.find('.tab-header .current-tab').attr('data-tab');
          // Check if there's a current tab and if it's not a right-sidebar tab
          if (currentTab != 'account' && currentTab != 'activity' &&  currentTab != undefined) {
            // Select current tab
            content.find('.dropdown-menu .'+currentTab).triggerHandler('click');
          } else {
            // Select dashboard tab
            content.find('.dropdown-menu .dashboard').triggerHandler('click');
          }
          // Append right sidebar tabs to right sidebar
          var rightSidebar = angular.element(document.getElementById('tab-body-sidebar'));
          rightSidebar.append(content.find('#account'));
          $compile(rightSidebar.contents())($scope)
          rightSidebar.append(content.find('#activity'));
          $compile(rightSidebar.contents())($scope)
        },0);
        // Update chart options
        if (window.graphic) {
          if (window.graphic.options.maintainAspectRatio) {
            window.graphic.options.maintainAspectRatio = false;
            window.graphic.update();
          }
        }
      } else {
        // If account it's on kickoff
        $timeout(function() {
          var content = angular.element(document.getElementById('content'));
          var currentTab = content.find('.tab-header .current-tab').attr('data-tab');
          // Check if current tab doesn't belong to right-sidebar
          if (currentTab != 'start-account' && currentTab != undefined) {
            // Select tab
            content.find('.dropdown-menu .'+currentTab).triggerHandler('click');
          } else {
            // Select kickoff tab
            content.find('.dropdown-menu .kickoff').triggerHandler('click');
          }
          // Select start account tab on right sidebar
          var startAccountTab = content.find('#start-account');
          startAccountTab.addClass('current-tab');
          var sidebarTabBody = angular.element(document.getElementById('tab-body-sidebar'));
          sidebarTabBody.append($compile(startAccountTab)($scope));
          $scope.$digest();
        },0);
      }
    }
  }

  // Event Listeners
  $rootScope.$on('responsive',function(e,width){
    vm.responsive(width);
  });

  // Events broadcast
  function closeLeftSidebar($event) {
    // Listener: LeftSidebarController
    $rootScope.$broadcast('closeLeftSidebar',$event);
  }

};})(); (function () {angular
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

}})(); (function () {angular
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

}})(); (function () {angular
  .module('instag-app')
  .controller('SettingsTabController',SettingsTabController);

// Controller services injection
SettingsTabController.$inject = ['$scope','$http','$timeout','$rootScope'];

function SettingsTabController ($scope,$http,$timeout,$rootScope) {

  var vm            = this;
  vm.changeSettings = changeSettings;
  vm.updateSetting  = updateSetting;
  // Events broadcast
  vm.accountStatus  = accountStatus;

  /*
    updateSetting() - Updates selected setting
    Function parameters:
    selectedOption: setting selected 
    setting: setting container id
  */
  function changeSettings($event,endpoint,id,speed) {
    if ($event) {
      var clickedElement = angular.element($event.currentTarget);                   // Clicked Element
    }
    angular.element(document.getElementById('loader')).addClass('loading');         // Show loader
        // Check if caller it's a speed setting
    var speed         = speed || false,
        cancelRequest = false,
        // Prepare post_data variable
        post_data     = {
                          account_id : id,
                          data : {}
                        };
    // Check For endpoint
    switch (endpoint) {
      case 'follow':
        clickedElement.parent().parent().find('button').html(clickedElement.html()+"<span class='caret'></span>");
        post_data.data.follows_max_limit = clickedElement.html();
        break;
      case 'unfollow-limit':
        clickedElement.parent().parent().find('button').html(clickedElement.html()+"<span class='caret'></span>");
        clickedElement.parent().parent().find('button').attr('data-limit',clickedElement.attr('data-limit'));
        post_data.data.follows_min_limit = clickedElement.attr('data-limit');
        break;
      case 'unfollow-source':
        clickedElement.parent().parent().find('button').html(clickedElement.html()+"<span class='caret'></span>");
        clickedElement.parent().parent().find('button').attr('data-source',clickedElement.attr('data-source'));
        post_data.data.unfollow_source = clickedElement.attr('data-source');
        break;
      case 'follow_speed':
        // Get speed Value
        var value = speed;
        // Get input parent element
        var elementContainer = angular.element(document.getElementById('follow-range'));
        // Check for value and assign data into two variables
        switch (value) {
          case "1":
            var selectedSpeed = "slow";
            var removeSpeeds = "medium fast";
            break;
          case "2":
            var selectedSpeed = "medium";
            var removeSpeeds = "slow fast";
            break;
          case "3":
            var selectedSpeed = "fast";
            var removeSpeeds = "slow medium";
            break;
        }
        // Get input parent element
        var elementContainer = angular.element(document.getElementById('follow-range'));
        // Find current speed span
        var selectedSpan = elementContainer.find('.'+selectedSpeed);
        // Hide displayed speed text and show current speed text
        var currentSpeed = elementContainer.find('.displayed');
        // If speed is aviable...
        if (selectedSpan.attr('data-valid') == '1') {
          if (currentSpeed.attr('data-speed-value') != undefined && currentSpeed.attr('data-speed-value') != value) {
            currentSpeed.removeClass('displayed');
            // Change input class for styling
            elementContainer.removeClass(removeSpeeds).addClass(selectedSpeed);
            elementContainer.find('.input-range').removeClass(removeSpeeds).addClass(selectedSpeed);
            selectedSpan.addClass('displayed');
            // Assign current speed to post_data variable
            post_data.data.follow_speed = selectedSpeed;
            // Change right sidebar speed
            angular.element(document.getElementById('right-sidebar-follow-speed')).html(selectedSpan.html());
          } else {
            var cancelRequest = true;
          }
        } else {
          // Show Upgrade modal
          var modal = angular.element(document.getElementById('upgrade-modal'));
          if (!modal.hasClass('displayed')) {
            modal.addClass('displayed');
          }
          var inputRange = elementContainer.find('.input-range');
          inputRange.removeClass("slow medium fast");
          inputRange.addClass(elementContainer.find('.displayed').attr('data-speed'));
          inputRange.val(elementContainer.find('.displayed').attr('data-speed-value'));
          inputRange.triggerHandler('change');
          angular.element(document.getElementById('follow-upgrade-plan')).addClass('displayed');
          var cancelRequest = true;
        }
        break;
      case 'unfollow_speed':
        // Get speed Value
        var value = speed;
        // Check for value and assign data into two variables
        switch (value) {
          case "1":
            var selectedSpeed = "slow";
            var removeSpeeds = "medium fast";
            break;
          case "2":
            var selectedSpeed = "medium";
            var removeSpeeds = "slow fast";
            break;
          case "3":
            var selectedSpeed = "fast";
            var removeSpeeds = "slow medium";
            break;
        }
        // Get input parent element
        var elementContainer = angular.element(document.getElementById('unfollow-range'));
        // Find current speed span
        var selectedSpan = elementContainer.find('.'+selectedSpeed);
        // Hide displayed speed text and show current speed text
        var currentSpeed = elementContainer.find('.displayed');
        // If speed is aviable...
        if (selectedSpan.attr('data-valid') == '1') {
          if (currentSpeed.attr('data-speed-value') != undefined && currentSpeed.attr('data-speed-value') != value) {
            currentSpeed.removeClass('displayed');
            // Change input class for styling
            elementContainer.removeClass(removeSpeeds).addClass(selectedSpeed);
            elementContainer.find('.input-range').removeClass(removeSpeeds).addClass(selectedSpeed);
            selectedSpan.addClass('displayed');
            // Assign current speed to post_data variable
            post_data.data.unfollow_speed = selectedSpeed;
            // Change right sidebar speed
            angular.element(document.getElementById('right-sidebar-unfollow-speed')).html(selectedSpan.html());
          } else {
            var cancelRequest = true;
          }
        } else {
          // Show Upgrade modal
          var modal = angular.element(document.getElementById('upgrade-modal'));
          if (!modal.hasClass('displayed')) {
            modal.addClass('displayed');
          }
          var inputRange = elementContainer.find('.input-range');
          inputRange.removeClass("slow medium fast");
          inputRange.addClass(elementContainer.find('.displayed').attr('data-speed'));
          inputRange.val(elementContainer.find('.displayed').attr('data-speed-value'));
          inputRange.triggerHandler('change');
          angular.element(document.getElementById('unfollow-upgrade-plan')).addClass('displayed');
          var cancelRequest = true;
        }
        break;
      case 'follow_no_private':
        clickedElement.parent().find('.active').removeClass('active');
        clickedElement.addClass('active');
        post_data.data.follow_no_private = parseInt(clickedElement.attr('data-private'));
        break;
      case 'sleep_status':
        clickedElement.parent().find('.active').removeClass('active');
        clickedElement.addClass('active');
        post_data.data.sleep_status = parseInt(clickedElement.attr('data-sleep'));
        break;
      case 'sleep_time':
        clickedElement.parent().parent().find('button').html(clickedElement.html()+"<span class='caret'></span>");
        clickedElement.parent().parent().find('button').attr('data-sleep',clickedElement.attr('data-sleep'));
        var sleepTime = parseInt(clickedElement.attr('data-sleep'));
        post_data.data.sleep_start_min = sleepTime;
        if (sleepTime <= 23 ) {
          post_data.data.sleep_start_max = sleepTime + 1;
        } else {
          post_data.data.sleep_start_max = (sleepTime + 1) - 24;
        }
        var sleepDuration = angular.element(document.getElementById('sleep-duration')).attr('data-sleep');
        var sleepEnd = sleepTime + parseInt(sleepDuration);
        if (sleepEnd <= 23) {
          post_data.data.sleep_end_min = sleepEnd;
          post_data.data.sleep_end_max = sleepEnd + 1;
        } else {
          post_data.data.sleep_end_min = sleepEnd - 24;
          post_data.data.sleep_end_max = (sleepEnd + 1) - 24;
        }
        break;
      case 'sleep_duration':
        clickedElement.parent().parent().find('button').html(clickedElement.html()+"<span class='caret'></span>");
        clickedElement.parent().parent().find('button').attr('data-sleep',clickedElement.attr('data-sleep'));
        var sleepTime = parseInt(angular.element(document.getElementById('sleep-time')).attr('data-sleep'));
        var sleepDuration = parseInt(clickedElement.attr('data-sleep'));
        var sleepEnd = sleepTime + sleepDuration;
        if (sleepEnd <= 23) {
          post_data.data.sleep_end_min = sleepEnd;
          post_data.data.sleep_end_max = sleepEnd + 1;
        } else {
          post_data.data.sleep_end_min = sleepEnd - 24;
          post_data.data.sleep_end_max = (sleepEnd + 1) - 24;
        }
        break;
      case 'like_status':
        if (clickedElement.hasClass('active')) {
          clickedElement.removeClass('active');
          post_data.data.like_status = '0';
        } else {
          clickedElement.addClass('active');
          post_data.data.like_status = '1';
        }
        break;
    }
    if (!cancelRequest) {
      $http.post('index.php?route=account/setting/edit', post_data).success(function(data){
        // Check for redirection
        if (data.redirect) {
          window.location = data.redirect;
        };
        angular.element(document.getElementById('loader')).removeClass('loading');
      });
    } else {
      angular.element(document.getElementById('loader')).removeClass('loading');
    }
  }

  /*
    updateSetting() - Updates selected setting
    Function parameters:
    selectedOption: setting selected 
    setting: setting container id
  */
  function updateSetting(selectedOption,setting) {
    $timeout(function() {
      // Get Dropdown element
      var dropdown = angular.element(document.getElementById(setting)).find('.dropdown');
      // Get options
      var options = dropdown.find('.dropdown-menu li');
      // Check for selected setting
      switch (setting) {
        case 'follow-limit-container':
          // Check for changed value
          if (parseInt(dropdown.find('.dropdown-toggle').html()) != selectedOption) {
            // Loop through list elements
            for (var i=0;i < options.length;i++) {
              // Check for same selected value 
              if (selectedOption == options.eq(i).html()) {
                // Trigger click on selected option
                options.eq(i).trigger('click');
              }
            }
          }
          break;
        case 'unfollow-limit-container':
          // Set selected value and check for content
          var selectedValue = selectedOption.replace( /^\D+/g,'');
          if (selectedValue == '')
            selectedValue = 0;
          // Get current value
          var dropdownVal = dropdown.find('.dropdown-toggle').attr('data-limit');
          // Check for changed value
          if (dropdownVal != selectedValue) {
            // Loop through list elements
            for (var i=0;i < options.length;i++) {
              // Check for same selected value 
              if (selectedValue == options.eq(i).attr('data-limit')) {
                // Trigger click on selected option
                options.eq(i).trigger('click');
              }
            }
          }
          break;
        case 'unfollow-source-container':
          // Get current value
          var dropdownVal = dropdown.find('.dropdown-toggle').attr('data-source');
          // Check for changed value
          if (dropdownVal != selectedOption) {
            // Loop through list elements
            for (var i=0;i < options.length;i++) {
              // Check for same selected value 
              if (selectedOption == options.eq(i).attr('data-source')) {
                // Trigger click on selected option
                options.eq(i).trigger('click');
              }
            }
          }
          break;
        case 'sleep-time-container':
          // Get Dropdown element
          var dropdown = angular.element(document.getElementById(setting)).find('.dropup');
          // Get options
          var options = dropdown.find('.dropdown-menu li');
          // Set selected value and check for content
          var selectedValue = selectedOption.replace( /^\D+/g,'');
          if (selectedValue == '')
            selectedValue = 0;
          // Get current value
          var dropdownVal = dropdown.find('.dropdown-toggle').attr('data-sleep');
          // Check for changed value
          if (dropdownVal != selectedValue) {
            // Loop through list elements
            for (var i=0;i < options.length;i++) {
              // Check for same selected value 
              if (selectedValue == options.eq(i).attr('data-sleep')) {
                // Trigger click on selected option
                options.eq(i).trigger('click');
              }
            }
          }
        case 'sleep-duration-container':
          // Get Dropdown element
          var dropdown = angular.element(document.getElementById(setting)).find('.dropup');
          // Get options
          var options = dropdown.find('.dropdown-menu li');
          // Set selected value and check for content
          var selectedValue = selectedOption.replace( /^\D+/g,'');
          if (selectedValue == '')
            selectedValue = 0;
          // Get current value
          var dropdownVal = dropdown.find('.dropdown-toggle').attr('data-sleep');
          // Check for changed value
          if (dropdownVal != selectedValue) {
            // Loop through list elements
            for (var i=0;i < options.length;i++) {
              // Check for same selected value 
              if (selectedValue == options.eq(i).attr('data-sleep')) {
                // Trigger click on selected option
                options.eq(i).trigger('click');
              }
            }
          }
      }
    },0);
  }

  // Event Listeners
  $rootScope.$on('changeSettings',function(e,$event,endpoint,id){
    vm.changeSettings($event,endpoint,id);
  });

  // Events broadcast
  function accountStatus($event,accountId) {
    // Listener: LeftSidebarController
    $rootScope.$broadcast('changeAccountStatus',$event,accountId);
  }

}})(); (function () {angular
  .module('instag-app')
  .controller('SourceInterestsController',SourceInterestsController);

// Controller services injection
SourceInterestsController.$inject = ['$scope','$http','$compile','$rootScope'];

function SourceInterestsController ($scope,$http,$compile,$rootScope) {
	var vm                                = this;
	vm.activateSource                     = activateSource;
  vm.checkUsersList                     = checkUsersList;
  vm.checkRequiredSources               = checkRequiredSources;
  vm.changeButtonLanguage               = changeButtonLanguage;
  // Flags
  vm.selectedSourcesFlag                = 0;
  // Extend angular.element object
  angular.element.prototype.checkIcon   = checkIcon;
  angular.element.prototype.uncheckIcon = uncheckIcon;

  /*
    activateSource() - Set selected sources as active
    $event = Selected source
  */
  function activateSource($event) {
    vm.selectedSourcesFlag = angular.element(document.getElementById('source-categories')).find('.active').length;
    // Get selected element
    var clickedElement = angular.element($event.currentTarget);
    // Get account id
    var accountId = angular.element(document.getElementById('left-sidebar')).find('.instagram-accounts .current-account').attr('data-id');
    // Get source id
    var sourceId = clickedElement.attr('data-source-id');
    if (!angular.element(document.getElementById('source-categories')).hasClass('disabled')) {
      angular.element(document.getElementById('loader')).addClass('loading');
      // Get selected status
      if (clickedElement.hasClass('active')) {
        var selectedStatus = 0;
      } else {
        var selectedStatus = 1;
      }
      // Set post data
      var post_data = {
        account_id: accountId,
        source_interest_id: sourceId,
        selected: selectedStatus
      };
      $http.post('index.php?route=account/account/source_interest',post_data).success(function(data){
        if (data.redirect) {
          window.location = data.redirect;
        }
        if (data.success) {
          // Get source interests count
          var sourcesCount = parseInt(angular.element(document.getElementById('users-notification-count')).html());
          if (selectedStatus) {
            // Change clicked button class
            clickedElement.addClass('active');
            // Increase source interests count
            sourcesCount++;
            vm.selectedSourcesFlag++;
            // Check if user has selected 5 or more sources
            if (vm.selectedSourcesFlag == 5) {
              angular.element(document.getElementById('source-categories')).addClass('disabled');
            }
            // Change button Language
            changeButtonLanguage('.users-steps',false);
            // Mark right sidebar user icon as checked
            angular.element(document.getElementById('rocket-item')).checkIcon('.right-sidebar-users i');
          } else{
            // Change clicked button class
            clickedElement.removeClass('active');
            // Decrease source interests count
            sourcesCount--;
            vm.selectedSourcesFlag--;
            // Mark right sidebar user icon as checked
            angular.element(document.getElementById('rocket-item')).uncheckIcon('.right-sidebar-users i');
          }
          // Check users or interest sources
          vm.checkRequiredSources();
          // Change source interests count
          angular.element(document.getElementById('tab-header-middle')).find('.users .notification-count').html(sourcesCount);
        }
        angular.element(document.getElementById('loader')).removeClass('loading');
      });
    } else {
      if (clickedElement.hasClass('active')) {
        angular.element(document.getElementById('loader')).addClass('loading');
        // Set post data
        var post_data = {
          account_id: accountId,
          source_interest_id: sourceId,
          selected: 0
        };
        $http.post('index.php?route=account/account/source_interest',post_data).success(function(data){
          if (data.success) {
            // Change clicked button class
            clickedElement.removeClass('active');
            // Get source interests count
            var sourcesCount = parseInt(angular.element(document.getElementById('users-notification-count')).html());
            // Decrease source interests count
            sourcesCount--;
            // Change source interests count
            angular.element(document.getElementById('tab-header-middle')).find('.users .notification-count').html(sourcesCount);
            // Decrease sources flag
            vm.selectedSourcesFlag--;
            if (vm.selectedSourcesFlag < 5) {
              angular.element(document.getElementById('source-categories')).removeClass('disabled')
            }
            // Check users or interest sources
            vm.checkRequiredSources();
            angular.element(document.getElementById('loader')).removeClass('loading');
          }
        });
      }
    }
  };

  /*
    checkUsersList() - Checks if there's any item added into a selected list
    Function parameters:
    listContainer: Selected list
  */
  function checkUsersList (listContainer) {
    var rocketItem = angular.element(document.getElementById('rocket-item'));
    // Check there's any other user added
    if (listContainer.children().length == 0) {
      switch (listContainer.attr('id')) {
        case 'user-sources-list':
          // Check Start account buttons
          checkRequiredSources();
          break;
        case 'whitelist-users-list':
          var whitelistSkip = angular.element(document.getElementById('start-kickoff-button')).attr('data-skip-whitelist');
          if (!whitelistSkip) {
            // Restore uncheck icon on right sidebar
            rocketItem.uncheckIcon('.right-sidebar-whitelist i');
            // Change button Language
            changeButtonLanguage('.whitelist-steps',true);
          } else {
            // Mark right sidebar user icon as checked
            rocketItem.checkIcon('.right-sidebar-whitelist i');
            // Change button Language
            changeButtonLanguage('.whitelist-steps',false);
          }
          // Check Start account buttons
          checkRequiredSources();
          break;
        case 'hashtag-sources-list':
          // Restore uncheck icon on right sidebar
          rocketItem.uncheckIcon('.right-sidebar-tags i');
          // Change button Language
          changeButtonLanguage('.tags-steps',true);
          break;
        case 'locations-sources-list':
          // Restore uncheck icon on right sidebar
          rocketItem.uncheckIcon('.right-sidebar-locations i');
          // Change button Language
          changeButtonLanguage('.locations-steps',true);
          break;
      }
    } else {
      // Change kickoff card language
      switch (listContainer.attr('id')) {
        case 'user-sources-list':
          // Mark right sidebar user icon as checked
          rocketItem.checkIcon('.right-sidebar-users i');
          // Check Start account buttons
          checkRequiredSources();
          // Change button Language
          changeButtonLanguage('.users-steps',false);
          break;
        case 'whitelist-users-list':
          // Mark right sidebar user icon as checked
          rocketItem.checkIcon('.right-sidebar-whitelist i');
          // Check Start account buttons
          checkRequiredSources();
          // Change button Language
          changeButtonLanguage('.whitelist-steps',false);
          break;
        case 'hashtag-sources-list':
          // Mark right sidebar user icon as checked
          rocketItem.checkIcon('.right-sidebar-tags i');
          // Change button Language
          changeButtonLanguage('.tags-steps',false);
          break;
        case 'locations-sources-list':
          // Mark right sidebar user icon as checked
          rocketItem.checkIcon('.right-sidebar-locations i');
          // Change button Language
          changeButtonLanguage('.locations-steps',false);
          break;
      }
    }
  }

  /*
    checkRequiredSources() - Checks if required sources are added for enabling/disabling the start account button
  */
  function checkRequiredSources() {
    var usersSources     = angular.element(document.getElementById('user-sources-list')).children(),
        sourcesInterest  = angular.element(document.getElementById('source-categories')).find('.row .active'),
        whitelistSources = angular.element(document.getElementById('whitelist-users-list')).children(),
        rightSidebarBtn  = angular.element(document.getElementById('start-kickoff-button')),
        tabsBtn          = angular.element(document.getElementById('tab-body-middle')).find('.start-account-button'),
        whitelistSkip    = rightSidebarBtn.attr('data-skip-whitelist');
    if ((usersSources.length || sourcesInterest.length) && (whitelistSources.length || whitelistSkip)) {
      // Enable start buttons
      rightSidebarBtn.removeAttr('disabled').removeClass('disabled-users disabled-whitelist');
      tabsBtn.removeAttr('disabled').removeClass('disabled-users disabled-whitelist');
      // Set start account language
      rightSidebarBtn.html(rightSidebarBtn.attr('data-start-account'));
      tabsBtn.html(tabsBtn.attr('data-start-account'));
      // Show start account button
      tabsBtn.removeClass('hide');
    } else {
      if ((!usersSources.length || !sourcesInterest.length) && (whitelistSources.length || whitelistSkip)) {
        // Enable start buttons
        rightSidebarBtn.removeAttr('disabled')
        tabsBtn.removeAttr('disabled');
        // Set add Users language
        rightSidebarBtn.html(rightSidebarBtn.attr('data-disabled-users'));
        tabsBtn.html(tabsBtn.attr('data-disabled-users'));
        // Add propper disabled classes
        rightSidebarBtn.addClass('disabled-users');
        tabsBtn.addClass('disabled-users');
        // Check if current tab is actually users and hide the button if so
        var currentTab = angular.element(document.getElementById('tab-dropdown-middle')).find('.current-tab');
        if (currentTab.attr('data-tab') == 'users') {
          tabsBtn.addClass('hide');
        }
      } else if ((usersSources.length || sourcesInterest.length) && (!whitelistSources.length || !whitelistSkip)) {
        // Enable start buttons
        rightSidebarBtn.removeAttr('disabled')
        tabsBtn.removeAttr('disabled');
        // Set add Whitelist language
        rightSidebarBtn.html(rightSidebarBtn.attr('data-disabled-whitelist'));
        tabsBtn.html(tabsBtn.attr('data-disabled-whitelist'));
        // Add propper disabled classes
        rightSidebarBtn.addClass('disabled-whitelist');
        tabsBtn.addClass('disabled-whitelist');
        // Check if current tab is actually whitelist and hide the button if so
        var currentTab = angular.element(document.getElementById('tab-dropdown-middle')).find('.current-tab');
        if (currentTab.attr('data-tab') == 'whitelist') {
          tabsBtn.addClass('hide');
        }
      } else {
        // Disable start buttons
        rightSidebarBtn.attr('disabled','true').removeClass('disabled-users disabled-whitelist');
        tabsBtn.attr('disabled','true').removeClass('disabled-users disabled-whitelist');
        // Set initial language
        rightSidebarBtn.html(rightSidebarBtn.attr('data-start-account'));
        tabsBtn.html(tabsBtn.attr('data-start-account'));
        if (!usersSources.length && !sourcesInterest.length) {
          //Restore uncheck icon on right sidebar
          angular.element(document.getElementById('rocket-item')).uncheckIcon('.right-sidebar-users i');
          // Change kickoff card button Language
          changeButtonLanguage('.users-steps',true);
        }
        // Show start account button
        tabsBtn.removeClass('hide');
      }


    }
  }

  /*
    changeButtonLanguage() - Changes selected button language
    Function parameters:
    cardClass: Selected button card class
  */
  function changeButtonLanguage(cardClass,getStarted) {
    var cardButton = angular.element(document.getElementById('kickoff')).find(cardClass+' .gold-button');
    if (getStarted) {
      cardButton.html(cardButton.attr('data-get-started'));
    } else {
      cardButton.html(cardButton.attr('data-add-more'));
    }
    
  }

  // Extend angular.element object
  function checkIcon(element) {
    this.find(element).removeClass('fa-chevron-right').addClass('fa-check-circle');
    return this;
  }

  function uncheckIcon(element) {
    this.find(element).removeClass('fa-check-circle').addClass('fa-chevron-right');
    return this;
  }

  // Events Listeners
  $rootScope.$on('checkUsersList',function(e,listContainer){
    vm.checkUsersList(listContainer);
  });

}})(); (function () {angular
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

}})(); (function () {angular
  .module('instag-app')
  .controller('TagsSourcesController',TagsSourcesController);

// Controller services injection
TagsSourcesController.$inject = ['$scope','$timeout','$http','$compile'];

function TagsSourcesController ($scope,$timeout,$http,$compile) {
}})(); (function () {angular
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

}})(); (function () {angular
  .module('instag-app')
  .controller('UsersSourcesController',UsersSourcesController);

// Controller services injection
UsersSourcesController.$inject = ['$scope','$timeout','$http','$compile'];

function UsersSourcesController ($scope,$timeout,$http,$compile) {
}})(); (function () {angular
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

}})(); (function () {})(); (function () {angular
  .module('instag-app')
  .service('chartService',chartService);

function chartService() {
  return {
    changeHeight: changeHeight,
    showChart: showChart
  }

  /* changeHeight() = Changes chart height */
  function changeHeight() {
    if (window.innerWidth > 730) {
      // Chart and My Influence cards
      var influence = angular.element(document.getElementById('my-influence')),
          chartCard = angular.element(document.getElementById('chart-card'));

      influence.innerHeight('auto');
      chartCard.innerHeight('auto');
      if (influence.innerHeight() > chartCard.innerHeight()) {
        chartCard.innerHeight(influence.innerHeight());
      } else {
        influence.innerHeight(chartCard.innerHeight());
      }
    }
  }

  /* showChart() = Shows chart on Dashboard Tab */
  function showChart() {
    if (angular.element(document.getElementById('chart-data')).length) {
      // Get data container
      var dataContainer = angular.element(document.getElementById('chart-data')),
          // Initialize graph variables
          graph         = [],
          labels        = [],
          datasets      = [],
          scales        = {},
          // Initialize alpha channel for data background
          alpha         = 3,
          // Initialize minimal step size
          stepSize      = 0,
          // Initialize max value from server
          maxValue      = 0;
          // Initialize limit Y-Axis Limit
          yLimit        = 0;

      // Loop through all data types (historical,current,projected)
      for (var i = 0; i < dataContainer.children().length; i++) {
          // Initialize graph instance and followers array
          graph[i] = {
              followers: []
          }
          // If this it's not the first data type, start with null value
          // https://github.com/chartjs/Chart.js/issues/2450
          if (i > 0) {
              // Skip previous 
              for (var p = 1; p < reportData.children().length; p++) {
                  graph[i].followers.push(null);
              }
          }
          // Get report data
          var reportData = dataContainer.children().eq(i);
          // Loop through all data items
          for (var p = 0; p < reportData.children().length; p++) {
              // Don't repeat date items on X-Axis
              if (labels.indexOf(reportData.children().eq(p).data('date')) == -1) {
                  labels.push(reportData.children().eq(p).data('date'));
              }
              // Add follower data to followers property on graph object
              graph[i].followers.push(reportData.children().eq(p).data('followers'));
              // Get step size
              var o = p+1;
              // Get sure next report data exists
              if (reportData.children().eq(o)) {
                // Check if stepSize is < to current and next repor data diff
                if (stepSize < (Math.abs(reportData.children().eq(o).data('followers')) - Math.abs(reportData.children().eq(p).data('followers'))))
                  stepSize = Math.abs(Math.abs(reportData.children().eq(o).data('followers')) - Math.abs(reportData.children().eq(p).data('followers')));
              }
              // Get Max value
              if (yLimit < reportData.children().eq(p).data('followers')) {
                yLimit = reportData.children().eq(p).data('followers');
              }
          }
          // Push dataset data to draw the graph
          datasets.push({
              label: dataContainer.children().eq(i).data('key'),
              data: graph[i].followers,
              //data: 371+i,
              backgroundColor: "rgba(129,55,176,0."+alpha+")",
              lineTension: 0.1,
              borderWidth: 1,
              borderColor: "rgba(0,0,0,0.3)",
              pointBorderColor: "rgba(129,55,176,0.7)",
              pointBackgroundColor: "rgba(129,55,176,1)"
          });
          // Increase alpha channel to get darker graph background
          alpha += 3;
      }
      // Round stepSize
      stepSize = (Math.ceil(stepSize/100)*100);
      // Get the max value
      maxValue = stepSize*(reportData.children().length+2);
      // Make sure yLimit it's not exaggeratedly large
      var condition = false;
      while (!condition) {
        if ((maxValue/yLimit)>2.1) {
          if ((maxValue-stepSize) > yLimit) {
            maxValue -= stepSize;
          } else {
            condition = true;
          }
        } else {
          condition = true; 
        }
      }

      // Check if chart element is there
      if (document.getElementById("chart")) {
          // Initialize chart
          var ctx = document.getElementById("chart");
          window.graphic = new Chart(ctx, {
              type: 'line',
              scales: {
                  position:'left',
                  height: 250
              },
              data: {
                  labels: labels,
                  datasets: datasets
              },
              options: {
                  maintainAspectRatio: false,
                  scales: {
                    yAxes: [{
                      ticks: {
                        scaleOverride: true,
                        stepSize: stepSize,
                        scaleStepWidth: Math.ceil(maxValue / stepSize)
                      }
                    }]
                  }
              }
          });
      }
    }
  }
}})(); (function () {angular
  .module('instag-app')
  .factory('checkStatusService',checkStatusService);

function checkStatusService () {
  return {
    checkStatus: checkStatus
  };

  /*
    checkStatus() - Checks for account status (Started | Stopped | Expired | Disabled)
    Function parameters:
    message: #content data-msg attribute
  */
  function checkStatus(message) {
    // Get current account element and right sidebar icon element
    var currentAccount   = angular.element(document.getElementById('left-sidebar')).find('.current-account'),
        statusIcon       = currentAccount.find('.account-status'),
        statusSquare     = currentAccount.find('.account-square-status'),
        rightSidebarIcon = angular.element(document.getElementById('account')).find('.run-status .account-status-icon'),
        // Set a list of classes to clear on elements
        removeClasses    = 'reconnect kickoff stopped started start stop disabled expired';
    // Check responsive and set headerStatus var to change the icon
    if (angular.element(window).innerWidth() < 1200) {
      var headerStatus = angular.element(document.getElementById('logo-u-container')).find('.account-item .account-status');
    }
    if (message == 'invalid_token') {
      // Disable left sidebar Start | Stop button
      currentAccount.attr('data-false','true');
      // Styles for left sidebar buttons
      statusIcon.removeClass(removeClasses).addClass('reconnect');
      statusSquare.removeClass(removeClasses).addClass('reconnect').attr('data-action','stopped');
      // Styles for right sidebar icon
      rightSidebarIcon.removeClass('fa-started fa-stopped fa-expired').addClass('fa-reconnect');
      // Update left sidebar Tooltip
      currentAccount.find('.account-square-status').attr('data-tooltip',rightSidebarIcon.attr('data-tooltip'));
      // Prepare z-index for modal and right sidebar
      angular.element(document.getElementById('reconnect-modal')).css('z-index','3');
      angular.element(document.getElementById('right-sidebar')).css('z-index','2');
      // Check headerStatus var
      if (headerStatus)
        headerStatus.removeClass(removeClasses).addClass('reconnect');
      // Modal settings 
      var username = currentAccount.find('.sidebar-item-title span').attr('data-username'),
          modal    = angular.element(document.getElementById('reconnect-modal'));
      modal.addClass('displayed');
      modal.find('.modal-header p span').html('@'+username);
      modal.find('#instagram_username').val(username);
    } else if (message == 'no_activity') {                      // No sources left
      // Disable left sidebar Start | Stop button
      currentAccount.attr('data-false','true');
      // Styles for left sidebar buttons
      statusIcon.removeClass(removeClasses).addClass('disabled');
      statusSquare.removeClass(removeClasses).addClass('disabled').attr('data-action','disabled');
      // Update left sidebar Tooltip
      statusSquare.attr('data-tooltip',rightSidebarIcon.attr('data-tooltip'));
      // Check headerStatus var
      if (headerStatus)
        headerStatus.removeClass(removeClasses).addClass('disabled');
    } else if (message == 'expired') {                          // Expired Account
      // Prepare z-index for modal and right-sidebar
      angular.element(document.getElementById('expired-modal')).css('z-index','3');
      angular.element(document.getElementById('right-sidebar')).css('z-index','2');
      // Modal settings 
      var modal = angular.element(document.getElementById('expired-modal'));
      modal.addClass('displayed');
      // Disable left sidebar Start | Stop button
      currentAccount.attr('data-false','true');
      // Styles for left sidebar buttons
      statusIcon.removeClass(removeClasses).addClass('expired');
      statusSquare.removeClass(removeClasses).addClass('expired').attr('data-action','expired');
      // Update left sidebar Tooltip
      statusSquare.attr('data-tooltip',rightSidebarIcon.attr('data-tooltip'));
      // Styles for right sidebar icon
      rightSidebarIcon.removeClass('fa-started fa-stopped fa-reconnect').addClass('fa-expired');
      // Check headerStatus var
      if (headerStatus)
        headerStatus.removeClass(removeClasses).addClass('expired');
    } else if (message == '' || message == 'temp_block') {                          // No message
      // Check for right sidebar button
      var action = angular.element(document.getElementById('change-status-btn')).attr('data-action');
      if (action == "start" || action == 'expired') {
        // Enable left sidebar button
        currentAccount.attr('data-false','');
        // Left sidebar styles
        statusIcon.removeClass(removeClasses).addClass('stopped');
        statusSquare.removeClass(removeClasses).addClass('stopped').attr('data-action','stopped');
        // Styles for right sidebar icon
        rightSidebarIcon.removeClass('fa-started fa-reconnect fa-expired').addClass('fa-stopped');
        // Check headerStatus var
        if (headerStatus)
          headerStatus.removeClass(removeClasses).addClass('stopped');
      } else if (action == "stop") {
        // Enable left sidebar button
        currentAccount.attr('data-false','');
        // Left sidebar styles
        statusIcon.removeClass(removeClasses).addClass('started');
        statusSquare.removeClass(removeClasses).addClass('started').attr('data-action','started');
        // Styles for right sidebar icon
        rightSidebarIcon.removeClass('fa-reconnect fa-stopped fa-expired').addClass('fa-started');
        // Check headerStatus var
        if (headerStatus)
          headerStatus.removeClass(removeClasses).addClass('started');
      }
    }
  }
}})(); (function () {angular
  .module('instag-app')
  .factory('modalsService',modalsService);

function modalsService() {
  return {
    displayWarning: displayWarning
  }

  /* displayWarning() - Displays warning modal called by container variable */
  function displayWarning(container) {
    angular.element(document.getElementById(container)).addClass('displayed');
  }

} })(); (function () {})(); (function () {angular
  .module('instag-app')
  .factory('timeAgoService',timeAgoService);

function timeAgoService() {
  return {
    timeAgo: timeAgo,
    historyMarkup: historyMarkup
  }

  /*
  timeAgo() - Calculates how much time has passed
  Function arguments:
  added = element added time
  offset = user configuration GTM offset
  */
  function timeAgo(added,offset) {
      // Get current time and calculate his offset value, same for element added time
      var now = new Date();
      var nowOffset = now.getTime() + (now.getTimezoneOffset()*60000);
      var addedOffset = (added-(parseInt(offset)*1000));
      var time = Math.round((nowOffset-addedOffset)/1000);
      // Check for time value
      switch (true) {
        case time < 60:
          time = document.getElementById('timeago-seconds').dataset.language;
          break;
        case time >= 60 && time < 120:
          time = document.getElementById('timeago-minute').dataset.language;
          break;
        case time >= 120 && time < 3600:
          time = Math.round(time/60).toString() + document.getElementById('timeago-minutes').dataset.language;
          break;
        case time >= 3600 && time <7200 :
          time = document.getElementById('timeago-hour').dataset.language;
          break;
        case time >= 7200 && time < 86400:
          time = Math.round((time/60)/60).toString() + document.getElementById('timeago-hours').dataset.language;
          break;
        case ((time/60)/60) >= 24 && ((time/60)/60) < 48:
          time = document.getElementById('timeago-day').dataset.language;
          break;
        case ((time/60)/60) >= 48:
          time = Math.round(((time/60)/60)/24).toString() + document.getElementById('timeago-days').dataset.language;
          break;
        default:
          time = '';
          break;
      }
      return time;
  }

  /* 
    historyMarkup() - Defines history items markup for account acctivity
    Function arguments:
    events = events elements
    singleEvent = defines if it's only one element
  */
  function historyMarkup(events,singleEvent){
    // Get singleEvent value
    var singleEvent = singleEvent || false;
    // Get offset value and initialize markup variables
    var offset = angular.element(document.getElementById('history-content')).attr('data-offset');
    var items = '';
    var time = '';
    // Check for a single event
    if (singleEvent) {
      // Save event data
      events.length = 1;  
      var added = new Date(events.date_added);
      var code = events.code;
      var title = events.title;
      var description = events.description;
    }
    // Loop through events elements
    for (var i=0;(i<events.length && i < 15);i++) {
      // If there's more than one element, save event data
      if (events.length > 1) {
        var added = new Date(events[i].date_added);
        var code = events[i].code;
        var title = events[i].title;
        var description = events[i].description;
      }
      // Get Time using timeAgo()
      time = timeAgo(added,offset);
      items += '<div class="history-item sidebar-item">';
      items += '<i class="fa fa-event-'+code+' history-icon"></i>';
      items += '<div class="item-content">';
      items += '<h4>'+title+'</h4>';
      items += '<p>'+description+'</p>';
      items += '<small data-time="'+added+'">'+time+'</small>';
      items += '</div>';
      items += '</div>';
    }
    return items;
  }

} })(); (function () {angular
  .module('instag-app')
  .controller('TooltipsController',TooltipsController);

// Controller services injection
TooltipsController.$inject = ['$scope','$rootScope'];

function TooltipsController($scope,$rootScope) {
  var vm = this;
  vm.displayTooltip     = displayTooltip;
  vm.hideTooltip        = hideTooltip;
  vm.triggerHideTooltip = triggerHideTooltip;
  vm.showChat           = showChat;


  /*
    displayTooltip() - Displays tooltip element
    Function parameters:
    $event: caller element
  */
  function displayTooltip($event) {
    // Get tooltip container
    var tooltipContainer = angular.element(document.getElementById('tooltip-container'));
    // Make sure tooltip it's not displayed
    if (!tooltipContainer.hasClass('display-tooltip')) {
      // Hide any tooltips
      vm.triggerHideTooltip();
      // Make tooltip visible
      tooltipContainer.addClass('display-tooltip');
      // Get caller tooltip settings (Position | Styles | Offset)
      var element = angular.element($event.currentTarget);
      var selectedHtmlObj = $event.currentTarget;
      var position = element.attr('data-tooltip-position');
      var styles = element.attr('data-tooltip-style');
      var offset = element.attr('data-tooltip-offset') || 0;
      var fire = element.attr('data-hide-fire') || 'mouseover';
      // Check for tooltip position and calculate it's CSS from it
      switch (position) {
        case 'top':
          var offsetY = (selectedHtmlObj.getBoundingClientRect().top - 10)+'px';
          var offsetX = (selectedHtmlObj.getBoundingClientRect().left + (selectedHtmlObj.offsetWidth/2))+'px';
          tooltipContainer.css({'top':offsetY,'left':offsetX,'transform':'translateY(-100%)'});
          break;
        case 'right':
          var offsetY = (selectedHtmlObj.getBoundingClientRect().top + (selectedHtmlObj.offsetHeight/2))+'px';
          var offsetX = (selectedHtmlObj.getBoundingClientRect().right + 25)+'px';
          tooltipContainer.css({'top':offsetY,'left':offsetX,'transform':'translatex(0)'});
          break;
        case 'bottom':
          var offsetY = (selectedHtmlObj.getBoundingClientRect().bottom - 10)+'px';
          var offsetX = (selectedHtmlObj.getBoundingClientRect().left + (selectedHtmlObj.offsetWidth/2))+'px';
          tooltipContainer.css({'top':offsetY,'left':offsetX,'transform':'translateY(50%)'});
          break;
        case 'left':
          var offsetY = (selectedHtmlObj.getBoundingClientRect().top + (selectedHtmlObj.offsetHeight/2))+'px';
          var offsetX = (selectedHtmlObj.getBoundingClientRect().left - 10 - offset)+'px';
          tooltipContainer.css({'top':offsetY,'left':offsetX,'transform':'translateX(-75%)'});
          break;
      }
      // Display tooltip container
      tooltipContainer.css('display','block');
      // Get tooltip element
      var tooltip = angular.element(document.getElementById('tooltip'));
      // Set it's content according to element setting
      tooltip.html(element.attr('data-tooltip'));
      // Clear tooltip position and assing the one from caller settings
      if (!tooltip.hasClass(position))
        tooltip.removeClass('top right bottom left').addClass(position);
      // Remove tooltip styles
        tooltip.removeClass('light dark wide').addClass(styles);
      // Set fire event
        tooltip.attr('data-hide-fire',fire);
      // Animate tooltip
      setTimeout(function(){
        tooltip.css({'opacity':'1','margin':'0px'});
      },10);
    }
  }

  /* hideTooltip() - Hides tooltip element */
  function hideTooltip($event) {
    // Get tooltip and tooltip container elements
    var tooltipContainer = angular.element(document.getElementById('tooltip-container')),
        tooltip          = angular.element(document.getElementById('tooltip'));
    // Check for event type and fire $event
    if ($event.type) {
      if (tooltip.attr('data-hide-fire') == $event.type) {
        // Remove styles, position, content, fire $event and CSS
        tooltip.removeAttr('style');
        tooltip.removeClass('top right bottom left dark light');
        tooltip.html('');
        tooltipContainer.removeAttr('style');
        tooltipContainer.removeAttr('data-hide-fire');
        // Hide tooltip
        tooltipContainer.removeClass('display-tooltip');
      }
    }
  }

  /* triggerHideTooltip() - Triggers hideTooltip() with a emulated event */
  function triggerHideTooltip() {
    // Get tooltip element
    var tooltip = angular.element(document.getElementById('tooltip'));
    // Check for event type and fire event
    if (tooltip.parent().hasClass('display-tooltip')) {
      var tooltipAttr = tooltip.attr('data-hide-fire')
      var emulatedEvent = { type: tooltipAttr };
      vm.hideTooltip(emulatedEvent);
    }
  }

  /*
    showChat() - Shows chat window
  */
  function showChat() {
    $zopim.livechat.window.show();
  }

  // Event Listeners
  $rootScope.$on('displayTooltip',function(e,$event){
    vm.displayTooltip($event);
  });

  $rootScope.$on('hideTooltip',function(e,$event){
    vm.hideTooltip($event);
  });

  $rootScope.$on('triggerHideTooltip',function(){
    vm.triggerHideTooltip();
  });

  $rootScope.$on('showChat',function(){
    vm.showChat();
  });
};})(); (function () {angular
	.module('instag-app')
	.factory('getEventTarget',getEventTarget);

function getEventTarget ($event) {
	if ($event.currentTarget) {
		var element = angular.element($event.currentTarget);
		return element;
	}
}})();