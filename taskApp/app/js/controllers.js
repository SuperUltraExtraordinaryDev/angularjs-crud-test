'use strict';

/* Controllers */

var tasksControllers = angular.module('tasksControllers', ['ui.bootstrap', 'ngStorage']);

// Task list controller (../partials/tasks-list.html)
tasksControllers.controller('TasksListCtrl', function ($scope, $rootScope, $http, Globals, $localStorage, $location, $timeout){

  // Init
  $scope.$storage = $localStorage;            // init localStorage
  $scope.token = $scope.$storage.token;       // get token from storage
  $scope.username = $scope.$storage.username; // get username from storage
  $scope.showCompletedTasks = false;          // hide completed tasks on load
  $scope.statuses = Globals.statuses;         // get all statusses (3 at the moment)
  $scope.orderProp = "created_at";            // sort on created_at on load 
  $scope.task = "";                           // init textbox task

  // Check access (timer, automatic log off and return to login page)
  $scope.checkAccess = function(){
    var data = {timestamp: new Date().getTime(), token: $scope.token  };
    $http.post(api_root + '/task/get_token_status', data).success(function(data) {
      $scope.tokenStatus = data.result;
      if($scope.tokenStatus != 'OK'){
        $scope.$storage.token = "";           // clear token from storage
        $timeout.cancel(checkAccessTimer);    // cancel timer
        $rootScope.LogoutOrLogin = 'Login';   // change button text to 'Login'
        $location.path('/login');             // redirect to login page
      } else {
        $rootScope.LogoutOrLogin = 'Logout';  // change button text to 'Logout'
      }
    });

    // repeat function every 60 sec.
    checkAccessTimer = $timeout($scope.checkAccess, 60000);
  };

  // Start check access timer (60 sec. delay)
  var checkAccessTimer = $timeout($scope.checkAccess, 60000);

  // Get tasks from API
  $scope.loadData = function(){
    var data = {timestamp: new Date().getTime(), token: $scope.token  };
    $http.post(api_root + '/task/get', data).success(function(data) {
      $scope.tasks = data;
    });
  };

  // Filter completed tasks (used for ng-repeat)
  $scope.filterCompleted = function(task){
    if($scope.showCompletedTasks === false){
      if(task.status < 3) {
              return true; 
          } else {
             return false; 
         }
     } else {
         return true;
     }
 };

  // Add task via API
  $scope.addTask = function() {
    if($scope.task.length > 0){
      var data = { task: $scope.task, 
             status: "1",
             token: $scope.token };
             $http.post(api_root + '/task/add', data).success(function (data, status) {
               $scope.loadData();
           }).success(function(){
               $scope.buttontext = 'Saved';
               $scope.buttonclass = 'btn btn-success';
               $scope.taskinputclass = '';
           });
       } else {
         $scope.buttontext = 'Add';
         $scope.buttonclass = 'btn btn-primary';
         $scope.taskinputclass = 'alert-danger';
     }
 };

  // Delete task via API
  $scope.deleteTask = function(taskId) {
    var data = { taskId: taskId, token:$scope.token };
    if(confirm('Delete task ' + taskId + '?','Please confirm')){
      $http.post(api_root + '/task/delete', data).success(function (data, status){
        $scope.loadData();
      });
       }
   };

  // Update task status via API
  $scope.updateTask = function(taskId, task, status) {
    var data = { taskId: taskId, task: task, status: status, token: $scope.token };
    $http.post(api_root + '/task/update', data).success(function (data, status) {
      $scope.loadData();
    });
  };
  
  // On load check access and load data
  $scope.checkAccess();
  $scope.loadData();

});


// Task detail controller (../partials/tasks-detail.html)
tasksControllers.controller('TaskDetailCtrl', function ($scope, $routeParams, $http, Globals, $localStorage){

  // Init
  $scope.$storage = $localStorage;    // init local storage
  $scope.token = $scope.$storage.token;   // get token from storage
  $scope.statuses = Globals.statuses;   // get all statusses (3 at the moment)
  $scope.buttontext = 'Update';     // set button text on load

  // Get task via API
  $scope.loadTask = function(){
    var data = {timestamp: new Date().getTime(), token: $scope.token};
    $http.post(api_root + '/task/get/' + $routeParams.taskId, data).success(function(data) {
      $scope.taskDetail = data[0];
      console.log($scope.taskDetail);
    });
  };

  // Update task details via API (all fields)
  $scope.updateTask = function() {
    var data = { taskId: $scope.taskDetail.taskId, 
          created_by: $scope.taskDetail.created_by,
          created_at: $scope.taskDetail.created_at,
          assigned_to: $scope.taskDetail.assigned_to,
          due_date: $scope.taskDetail.due_date, 
          task: $scope.taskDetail.task, 
          status: $scope.taskDetail.status,
          token: $scope.token };
    // todo, try: http://docs.angularjs.org/api/ng/function/angular.toJson
    $http.post(api_root + '/task/update', data).success(function (data, status) {
      console.log(data);
    });
  };

  // Get task on load
  $scope.loadTask();


});

// Login/logoff controller (../partials/login.html)
tasksControllers.controller('LoginCtrl', function ($scope, $rootScope, $http, $localStorage, $location){

  // Init 
  $scope.$storage = $localStorage;     // Init localStorage
  $scope.$storage.token = "";          // Clear token (login also logoff)
  $rootScope.LogoutOrLogin = 'Login';  // Change button text to 'Login'

  // Get task via API
  $scope.login = function(){
    var data = {username: $scope.username, password: $scope.password};
    $http.post(api_root + '/auth/validate_credentials', data).success(function(data) {
      $scope.$storage.token = data.token;       // put token in storage
      $scope.$storage.username = data.username; // put username in storage

      // If token, then login OK
      if($scope.$storage.token){          
        $rootScope.LogoutOrLogin = 'Logout';  // change button text to 'Logoff'
        $location.path('#/tasks');            // redirect to tasks
      }
      
    });
  };


});
