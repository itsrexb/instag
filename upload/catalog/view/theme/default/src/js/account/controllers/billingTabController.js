angular
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

}