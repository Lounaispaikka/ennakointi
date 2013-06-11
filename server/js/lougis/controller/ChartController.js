/**
*ChartController
*ChartController.js
*@author Ville Glad
**/
Ext.define('Lougis.controller.ChartController', {
	extend: 'Ext.app.Controller',
	
	views: [
		'Charts'
	],
	stores: ['ChartTreeStore'],
	models: ['ChartTreeModel'],
	
	init: function() {
		console.log('Hipheichart');
			
	}
	
});