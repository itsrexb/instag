angular
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

}