angular
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

};