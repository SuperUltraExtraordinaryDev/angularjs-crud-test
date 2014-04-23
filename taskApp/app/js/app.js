'use strict';

/* App Module */

// set this to your API root
var api_root = '/angularjs-crud-test/taskAPI';

var tasksApp = angular.module('tasksApp', [
  'ngRoute',
  'ngCookies',
  'ngStorage',
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
        controller: 'TaskDetailCtrl'
      }).
      when('/login', {
          templateUrl: 'partials/login.html',
          controller: 'LoginCtrl'
      }).
      otherwise({
        redirectTo: '/tasks'
      });
  }]);
