angular
  .module('instag-app')
  .controller('TagsSourcesController',TagsSourcesController);

// Controller services injection
TagsSourcesController.$inject = ['$scope','$timeout','$http','$compile'];

function TagsSourcesController ($scope,$timeout,$http,$compile) {
}