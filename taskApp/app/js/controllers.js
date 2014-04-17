'use strict';

/* Controllers */

var tasksControllers = angular.module('tasksControllers', ['ui.bootstrap']);

var api_root = '/angularjs-crud-test/taskAPI';

tasksControllers.factory('Globals', function() {
	return {
		statuses : [
			{value:'1', label:'Open'},
			{value:'2', label:'Pending'},
			{value:'3', label:'Completed'}
		]
	};
});

tasksApp.directive('bsdatepicker', function(){
	return {
		require: '?ngModel',
		link: function (scope, element, attrs, ngModel) {
			scope.$watch(element, function() {
		   		$(element).datepicker({
		   			format:'yyyy-mm-dd'
		   		}).on('changeDate', function() {
		   			scope.$apply( function() {
		   				ngModel.$setViewValue(element.val());
		   			});
		   			$(element).datepicker('hide');		   			
		   		});
			});
		}
	}
});

tasksControllers.controller('TasksListCtrl', function ($scope, $http, Globals){

	$scope.showCompletedTasks = false;

	$scope.statuses = Globals.statuses;

	$scope.loadData = function(){
		$http.get(api_root + '/task/get'  + "?" + new Date().getTime() ).success(function(data) {
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

tasksControllers.controller('TaskDetailCtrl', function ($scope, $routeParams, $http, Globals){

	$scope.statuses = Globals.statuses;
	$scope.buttontext = 'Update';

	$scope.loadTask = function(){
		$http.get(api_root + '/task/get/' + $routeParams.taskId).success(function(data) {
			$scope.taskDetail = data[0];
			console.log($scope.taskDetail);
		});
	}

	$scope.updateTask = function() {
		var data = { taskId: $scope.taskDetail.taskId, 
					 created_at: $scope.taskDetail.created_at,
					 due_date: $scope.taskDetail.due_date, 
					 task: $scope.taskDetail.task, 
					 status: $scope.taskDetail.status };
		// todo, try: http://docs.angularjs.org/api/ng/function/angular.toJson
		$http.post(api_root + '/task/update', data).success(function (data, status) {
			console.log(data);
		});
	}

	$scope.loadTask();


});
