'use strict';

/* App Module */

var tasksApp = angular.module('tasksApp', [
  'ngRoute',
  'tasksControllers'
]);

tasksApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/tasks', {
        templateUrl: 'partials/tasks-list.html',
        controller: 'TasksListCtrl'
      }).
      when('/tasks/:taskId', {
        templateUrl: 'partials/tasks-detail.html',
        controller: 'TasksDetailCtrl'
      }).
      otherwise({
        redirectTo: '/tasks'
      });
  }]);
