angular
  .module('instag-app')
  .controller('CommonController',CommonController);

// Controller services injection
CommonController.$inject = ['$http','$compile','$scope','$window','$timeout'];

function CommonController ($http, $compile, $scope, $window, $timeout) {
  var vm = this;
  vm.checkBox        = checkBox;
  vm.openModal       = openModal;
  vm.closeModal      = closeModal;
  vm.checkNewsletter = checkNewsletter;


  function checkBox() {
    var checkbox = angular.element(document.getElementById('checkbox-remember'));
    if (checkbox.hasClass('checked')) {
      checkbox.removeClass('checked');
    } else {
      checkbox.addClass('checked');
    }
  };

  function openModal(modalId) {
    angular.element(document.getElementById(modalId)).addClass('displayed');
  };

  function closeModal($event) {
    var clickedElement = angular.element($event.currentTarget);
    clickedElement.parent().parent().parent().parent().removeClass('displayed');
  };

  function checkNewsletter() {
    var element = angular.element(document.getElementById('newsletter')).find('.check');
    if (!element.hasClass('checked')) {
      element.addClass('checked');
      angular.element(document.getElementById('newsletter-input')).val('1');
    } else {
      element.removeClass('checked');
      angular.element(document.getElementById('newsletter-input')).val('0');
    }
  }
}

function getURLVar(key) {
  var value = [];

  var query = String(document.location).split('?');

  if (query[1]) {
    var part = query[1].split('&');

    for (i = 0; i < part.length; i++) {
      var data = part[i].split('=');

      if (data[0] && data[1]) {
        value[data[0]] = data[1];
      }
    }

    if (value[key]) {
      return value[key];
    } else {
      return '';
    }
  }
}