angular
  .module('instag-app')
  .controller('LoadController',LoadController);

// Controller services injection  
LoadController.$inject = ['$scope']; 

function LoadController($scope) {
  var vm      = this;
  vm.loadFn   = loadFn;
  vm.showCols = showCols;
  vm.showTab  = showTab;

  $scope.load = vm.loadFn();

  /* loadFn() - makes the proper behavior when page it's loaded */
  function loadFn() {
    angular.element(document.getElementById('loader')).css('display','none');

    var scrollContainer = angular.element(window);

    scrollContainer.works,
    scrollContainer.plan,
    scrollContainer.grow,
    scrollContainer.measure,
    scrollContainer.devices = false;
    // Set height offset
    if (window.innerWidth > 760) {
      offset = 300;
    } else {
      offset = 0;
    }
    scrollContainer.bind("scroll", function() {
      if (!angular.element(document.getElementById('content')).hasClass('no-fixed')) {
        // Get Fixed header
        if (scrollContainer.scrollTop() > 60 && !angular.element(document.getElementById('header')).hasClass('fixed')) {
          angular.element(document.getElementById('header')).addClass('fixed');
        } else if (scrollContainer.scrollTop() == 0) {
          angular.element(document.getElementById('header')).removeClass('fixed');
        };
        // Check for current page == home
        if (angular.element(document.getElementById('header')).parent().hasClass('common-home')) {
          // "How Does It Work?" animation
          if ((scrollContainer.innerHeight() - offset) > document.getElementById('plan-col').getBoundingClientRect().top && !scrollContainer.works) {
            scrollContainer.works = true;
            vm.showCols();
          }
          // "Plan Your Strategy" animation
          if ((scrollContainer.innerHeight() - offset) > document.getElementById('plan-tabs').getBoundingClientRect().top && !scrollContainer.plan) {
            scrollContainer.plan = true;
            showTab('users');
          }
          // "Grow Your Account" animation
          if ((scrollContainer.innerHeight() - offset) > document.getElementById('grow-tabs').getBoundingClientRect().top && !scrollContainer.grow) {
            scrollContainer.grow = true;
            showTab('follow');
          }
          // "Measure Your results" animation
          if ((scrollContainer.innerHeight() - offset) > document.getElementById('measure').getBoundingClientRect().top && !scrollContainer.measure) {
            scrollContainer.measure = true;
            showTab('measure');
          }
          // "From any Device" animation
          if ((scrollContainer.innerHeight() - offset) > document.getElementById('devices').getBoundingClientRect().top && !scrollContainer.devices) {
            scrollContainer.devices = true;
            showTab('devices');
          }
        }
      }
    });
  }

  /* loadFn() - show columns in the homepage */
  function showCols() {
    var colContainer = angular.element(document.getElementById('home-how-works')).find('.container .col-item');
    var i = 0;
    setInterval(function(){
      if (i <= 3) {
        var colContainer = angular.element(document.getElementById('home-how-works')).find('.container .col-item');
        colContainer.eq(i).removeClass('appear-top').css('top','0');
        i++;
      }
    },200);
  }

  function showTab(id) {
    var tabContainer = angular.element(document.getElementById(id)).find('img');
    if (id == 'measure') {
      tabContainer.removeClass('appear-top').css('bottom','0px');
    } else if (id == 'devices') { 
      tabContainer.removeClass('appear-right').css('transform','translateX(50%)');
    } else {
      tabContainer.removeClass('appear-top').css('bottom','-30px');
    }
  }

}