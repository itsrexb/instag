angular
  .module('instag-app')
  .controller('LoginController',LoginController);

// Controller services injection
LoginController.$inject = ['$http','$timeout'];

function LoginController ($http, $timeout) {
  var vm = this;
  vm.closeLogin = closeLogin;
  vm.login      = login;

  function closeLogin($event) {
    angular.element(document.getElementById('login-modal')).css('opacity','0').removeClass('display');
  }

  function login() {
    var email    = angular.element(document.getElementById('login-email')).val(),
        password = angular.element(document.getElementById('login-password')).val();

    angular.element(document.getElementById('login-form-container')).find('button').addClass('loading-button').attr('disabled',true);

    var post_data = {
      email:    email,
      password: password
    };

    $http.post('index.php?route=customer/login/json', post_data).success(function(data) {
      if (data.success) {
        window.location.href = data.redirect;
      } else {
        angular.element(document.getElementById('login-error')).attr('data-tooltip',data.error_warning);
        $timeout(function() {
          angular.element(document.getElementById('login-error')).trigger('click');
        });
        angular.element(document.getElementById('login-form-container')).find('button').removeClass('loading-button').removeAttr('disabled');
      }
    });
  };
}