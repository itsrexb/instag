angular
  .module('instag-app')
  .controller('UsersSourcesController',UsersSourcesController);

// Controller services injection
UsersSourcesController.$inject = ['$scope','$timeout','$http','$compile'];

function UsersSourcesController ($scope,$timeout,$http,$compile) {
}