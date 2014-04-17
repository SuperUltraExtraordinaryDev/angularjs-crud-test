'use strict';

/* Directives */

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