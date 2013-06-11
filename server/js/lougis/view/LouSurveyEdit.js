/**
*View
*LouSurveyEdit.js
*@author Ville Glad
**/
Ext.define('Lougis.view.LouSurveyEdit', {
	extend: 'Lougis.view.Panel',
	alias: 'widget.lousurveyedit',
	
	title: 'Muokkaa kyselylomaketta',
	
	anchor: '100% 100%',
	border: 0,
	
	initComponent: function() {	
		this.callParent();
		this.LouSurveyEditPanel = Ext.create('Ext.panel.Panel', {
			layout: 'border',
			border: 0,
			anchor: '100% 100%'
		});
		console.log("Hiphei LouSurveyEdit");
	}
	
	
});