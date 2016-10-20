angular
  .module('instag-app')
  .controller('LocationsController',LocationsController);

// Controller services injection
LocationsController.$inject = ['$scope','$timeout','$http','$compile','$rootScope'];

function LocationsController ($scope,$timeout,$http,$compile,$rootScope) {
	
}