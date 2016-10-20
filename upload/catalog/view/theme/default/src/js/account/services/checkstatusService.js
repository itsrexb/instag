angular
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
}