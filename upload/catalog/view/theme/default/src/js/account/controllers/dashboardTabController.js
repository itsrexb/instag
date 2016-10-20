angular
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

}