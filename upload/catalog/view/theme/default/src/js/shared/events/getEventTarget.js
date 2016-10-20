angular
	.module('instag-app')
	.factory('getEventTarget',getEventTarget);

function getEventTarget ($event) {
	if ($event.currentTarget) {
		var element = angular.element($event.currentTarget);
		return element;
	}
}