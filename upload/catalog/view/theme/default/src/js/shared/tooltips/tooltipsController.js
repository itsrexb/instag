angular
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
};