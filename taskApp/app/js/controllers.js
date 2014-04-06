'use strict';

/* Controllers */

var tasksControllers = angular.module('tasksControllers', []);

tasksControllers.controller('TasksListCtrl', function ($scope, $http){

	$scope.loadData = function(orderProp='taskId'){
		$http.get('/angularjs-crud-test/taskAPI/task/get').success(function(data) {
			$scope.tasks = data;
		});
		$scope.orderProp = orderProp;
	}

	$scope.addTask = function() {
		var data = { task: $scope.task, status: "1" };
		$http.post("/angularjs-crud-test/taskAPI/task/add", data).success(function (data, status) {
			// console.log(data);
			// $scope.tasks.push.apply($scope.tasks, data);
			$scope.loadData();
		});
	}

	$scope.deleteTask = function(taskId) {
		var data = { taskId: taskId };
		if(confirm('Delete task ' + taskId + '?','Question')){
			$http.post("/angularjs-crud-test/taskAPI/task/delete", data).success(function (data, status){
				$scope.loadData();
			});
    	}
	}

	$scope.updateTask = function(taskId, task, status) {
		var data = { taskId: taskId, task: task, status: status };
		$http.post("/angularjs-crud-test/taskAPI/task/update", data).success(function (data, status) {
			console.log(data);
			$scope.loadData();
			//$scope.tasks.push.apply($scope.tasks, data);
		});
	}

	$scope.loadData();

});

tasksControllers.controller('TasksDetailCtrl', ['$scope', '$routeParams',
	function($scope, $routeParams) {
		$scope.taskId = $routeParams.taskId;
	}]);







