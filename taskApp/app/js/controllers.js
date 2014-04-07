'use strict';

/* Controllers */

var tasksControllers = angular.module('tasksControllers', []);

var rooturl = '/angularjs-crud-test';

tasksControllers.controller('TasksListCtrl', function ($scope, $http){

	$scope.loadData = function(orderProp='taskId'){
		$http.get(rooturl + '/taskAPI/task/get').success(function(data) {
			$scope.tasks = data;
			console.log(data);
		});
		$scope.orderProp = orderProp;
	}

	$scope.addTask = function() {
		var data = { task: $scope.task, status: "1" };
		$http.post(rooturl + '/taskAPI/task/add', data).success(function (data, status) {
			$scope.loadData();
			// Alternative: $scope.tasks.push.apply($scope.tasks, data);
			console.log(data);
		});
	}

	$scope.deleteTask = function(taskId) {
		var data = { taskId: taskId };
		if(confirm('Delete task ' + taskId + '?','Please confirm')){
			$http.post(rooturl + '/taskAPI/task/delete', data).success(function (data, status){
				$scope.loadData();
				console.log(data);
			});
    	}
	}

	$scope.updateTask = function(taskId, task, status) {
		var data = { taskId: taskId, task: task, status: status };
		$http.post(rooturl + '/taskAPI/task/update', data).success(function (data, status) {
			$scope.loadData();
			console.log(data);
		});
	}

	$scope.loadData();

});

tasksControllers.controller('TasksDetailCtrl', ['$scope', '$routeParams',
	function($scope, $routeParams) {
		$scope.taskId = $routeParams.taskId;
	}]);







