'use strict';

/* Controllers */

var tasksControllers = angular.module('tasksControllers', ['ui.bootstrap']);

var api_root = '/angularjs-crud-test/taskAPI';

tasksControllers.controller('TasksListCtrl', function ($scope, $http){

	$scope.showCompletedTasks = false;
	
	// ... fix this (repeating code, better angular way?) ...
	var statuses = new Array();
	statuses[1] = 'Open';
	statuses[2] = 'Pending';
	statuses[3] = 'Completed';
	$scope.statuses = statuses;

	$scope.loadData = function(){
		$http.get(api_root + '/task/get').success(function(data) {
			$scope.tasks = data;
			console.log(data);
		});
		$scope.orderProp = "taskId";
	}

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

	$scope.addTask = function() {
		var data = { task: $scope.task, status: "1" };
		$http.post(api_root + '/task/add', data).success(function (data, status) {
			$scope.loadData();
			// Alternative: $scope.tasks.push.apply($scope.tasks, data);
			console.log(data);
		});
	}

	$scope.deleteTask = function(taskId) {
		var data = { taskId: taskId };
		if(confirm('Delete task ' + taskId + '?','Please confirm')){
			$http.post(api_root + '/task/delete', data).success(function (data, status){
				$scope.loadData();
				console.log(data);
			});
    	}
	}

	$scope.updateTask = function(taskId, task, status) {
		var data = { taskId: taskId, task: task, status: status };
		$http.post(api_root + '/task/update', data).success(function (data, status) {
			$scope.loadData();
			console.log(data);
		});
	}

	$scope.loadData();

});

tasksControllers.controller('TaskDetailCtrl', function ($scope, $routeParams, $http){

	$scope.loadTask = function(){
		$http.get(api_root + '/task/get/' + $routeParams.taskId).success(function(data) {
			$scope.taskDetail = data[0];
			console.log($scope.taskDetail);
		});
		$scope.statuses = [
			{value:'1', label:'Open'},
			{value:'2', label:'Pending'},
			{value:'3', label:'Completed'}
		];
		$scope.buttontext = 'Update';
	}

	$scope.updateTask = function() {
		var data = { taskId: $scope.taskDetail.taskId, created_at: $scope.taskDetail.created_at, task: $scope.taskDetail.task, status: $scope.taskDetail.status };
		// todo, try: http://docs.angularjs.org/api/ng/function/angular.toJson
		$http.post(api_root + '/task/update', data).success(function (data, status) {
			console.log(data);
		});

	}

	$scope.$watch("created_at", function() {
   		$('#created_at').datepicker({
   			format:'yyyy-mm-dd'
   		}).on('changeDate', function(ev) {
   			$('#created_at').datepicker('hide').data('datepicker');
   			$scope.taskDetail.created_at = $('#created_at').val();
   		});
	});

	$scope.loadTask();

	

});








