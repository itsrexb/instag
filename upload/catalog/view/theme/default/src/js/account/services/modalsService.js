angular
  .module('instag-app')
  .factory('modalsService',modalsService);

function modalsService() {
  return {
    displayWarning: displayWarning
  }

  /* displayWarning() - Displays warning modal called by container variable */
  function displayWarning(container) {
    angular.element(document.getElementById(container)).addClass('displayed');
  }

} 