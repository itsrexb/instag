angular
  .module('instag-app')
  .controller('CustomerRegisterController',CustomerRegisterController);

// Controller services injection  
//HeaderController.$inject = [];

function CustomerRegisterController() {
  var vm        = this;
  vm.showLoader = showLoader;
  
  function showLoader($event) {
    var clickedElement = angular.element($event.currentTarget),
        registerForm = angular.element(document.getElementById('register-form'));
    if (registerForm.find('#input-firstname').val().length) {
      if (registerForm.find('#input-lastname').val().length) {
        if (registerForm.find('#input-email').val().length) {
          if (registerForm.find('#input-email').val().indexOf('@') > -1) {
            if (registerForm.find('#input-telephone').val().length) {
              if (registerForm.find('#input-password').val().length) {
                if (registerForm.find('#input-confirm').val().length) {
                  clickedElement.attr('disabled',true);
                  angular.element(document.getElementById('loader')).addClass('loading');
                  registerForm.submit();
                }
              }
            }
          }
        }
      }
    }
  };
}