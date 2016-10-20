angular
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

}