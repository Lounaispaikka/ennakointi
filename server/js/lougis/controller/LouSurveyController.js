/**
*Controller
*LouSurveyController.js
*@author Ville Glad
**/
Ext.define('Lougis.controller.LouSurveyController', {
	extend: 'Ext.app.Controller',
	
	views: [
		'LouSurvey'
	],
	stores: ['LouSurveyTreeStore'],
	models: ['LouSurveyTreeModel'],
	
	init: function() {
		console.log('Hiphei');
	}
	
});