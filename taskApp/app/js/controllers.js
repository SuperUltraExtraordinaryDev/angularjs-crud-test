'use strict';

/* Controllers */

var tasksControllers = angular.module('tasksControllers', ['ui.bootstrap', 'ngStorage']);
																			

// Task list controller (../partials/tasks-list.html)
tasksControllers.controller('TasksListCtrl', function ($scope, $rootScope, $http, Globals, $localStorage, $location, $timeout){
																			
	// Initialize variables
	$scope.$storage = $localStorage;
	$scope.token = $scope.$storage.token;
	$scope.username = $scope.$storage.username;  
	$scope.showCompletedTasks = false;	// hide completed tasks on load
	$scope.statuses = Globals.statuses;	// get all statusses (3 at the moment)
	$scope.orderProp = "created_at";	// sort on created_at on load 

	// Check access (timer, automatic log off and return to login page)
	$scope.checkAccess = function(){
		var data = {timestamp: new Date().getTime(), token: $scope.token  };
		//$http.post(api_root + '/auth/get_token_status', data).success(function(data) {
		$http.post(api_root + '/task/get_token_status', data).success(function(data) {
			$scope.tokenStatus = data.result;
			if($scope.tokenStatus != 'OK'){
				$scope.$storage.token = "";
				$timeout.cancel(checkAccessTimer);
				$rootScope.LogoutOrLogin = 'Login';
				$location.path('/login');
			} else {
				$rootScope.LogoutOrLogin = 'Logout';
			}
			//console.log(data);
		});

		checkAccessTimer = $timeout($scope.checkAccess, 60000);
	}

	// Start check access timer
	var checkAccessTimer = $timeout($scope.checkAccess, 60000);

	// Get tasks from API
	$scope.loadData = function(){
		var data = {timestamp: new Date().getTime(), token: $scope.token  };
		$http.post(api_root + '/task/get', data).success(function(data) {
			$scope.tasks = data;
			//console.log(data);
		});
	}

	// Filter completed tasks (used for ng-repeat)
	$scope.filterCompleted = function(task){
		if($scope.showCompletedTasks == false){
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
		var data = { task: $scope.task, status: "1", token: $scope.token };
		$http.post(api_root + '/task/add', data).success(function (data, status) {
			$scope.loadData();
			//console.log(data);
		});
	}

	// Delte task via API
	$scope.deleteTask = function(taskId) {
		var data = { taskId: taskId, token:$scope.token };
		if(confirm('Delete task ' + taskId + '?','Please confirm')){
			$http.post(api_root + '/task/delete', data).success(function (data, status){
				$scope.loadData();
				//console.log(data);
			});
    	}
	}

	// Update task status via API
	$scope.updateTask = function(taskId, task, status) {
		var data = { taskId: taskId, task: task, status: status, token: $scope.token };
		$http.post(api_root + '/task/update', data).success(function (data, status) {
			$scope.loadData();
			//console.log(data);
		});
	}
	
	// Load task list on load
	$scope.checkAccess();
	$scope.loadData();

});


// Task detail controller (../partials/tasks-detail.html)
tasksControllers.controller('TaskDetailCtrl', function ($scope, $routeParams, $http, Globals, $localStorage){

	// Initialize variables
	$scope.$storage = $localStorage;
	$scope.token = $scope.$storage.token;
	$scope.statuses = Globals.statuses; // get all statusses (3 at the moment)
	$scope.buttontext = 'Update';		// set button text on load

	// Get task via API
	$scope.loadTask = function(){
		var data = {timestamp: new Date().getTime(), token: $scope.token};
		$http.post(api_root + '/task/get/' + $routeParams.taskId, data).success(function(data) {
			$scope.taskDetail = data[0];
			console.log($scope.taskDetail);
		});
	}

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
	}

	// Get task on load
	$scope.loadTask();


});

// Login controller (../partials/login.html)
tasksControllers.controller('LoginCtrl', function ($scope, $rootScope, $http, $localStorage, $location){

	// Init localStorage
	$scope.$storage = $localStorage;
	
	// Clear token (login = logoff)
	$scope.$storage.token = "";
	$rootScope.LogoutOrLogin = 'Login';

	// Get task via API
	$scope.login = function(){
		var data = {username: $scope.username, password: $scope.password};
		//$http.post(api_root + '/auth/validate_credentials', data).success(function(data) {
		$http.post(api_root + '/auth/validate_credentials', data).success(function(data) {
			$scope.$storage.token = data.token;
			$scope.$storage.username = data.username
			if($scope.$storage.token){	
				$rootScope.LogoutOrLogin = 'Logout';
				$location.path('#/tasks');
			}
		});
	}


});
