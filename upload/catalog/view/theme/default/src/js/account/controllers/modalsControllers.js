angular
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

}