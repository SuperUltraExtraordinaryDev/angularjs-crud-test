'use strict';

/* Services */

tasksApp.factory('Globals', function() {
  return {
    statuses : [
    {value:'1', label:'Open'},
    {value:'2', label:'Pending'},
    {value:'3', label:'Completed'}
    ]
  };
});