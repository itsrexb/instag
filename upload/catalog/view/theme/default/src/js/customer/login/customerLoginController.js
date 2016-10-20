angular
  .module('instag-app')
  .controller('CustomerLoginController',CustomerLoginController);

// Controller services injection  
//HeaderController.$inject = [];

function CustomerLoginController() {
  var vm        = this;
  vm.showLoader = showLoader;
  
  function showLoader($event) {
    var clickedElement = angular.element($event.currentTarget),
        loginForm = angular.element(document.getElementById('login-form'));
    if (loginForm.find('#input-email').val().length) {
      if (loginForm.find('#input-email').val().indexOf('@') > -1) {
        if (loginForm.find('#input-password').val().length) {
          clickedElement.attr('disabled',true);
          angular.element(document.getElementById('loader')).addClass('loading');
          loginForm.submit();
        }
      }
    }
  };

}