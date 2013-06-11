Ext.define('Lougis.model.LouSurveyTreeModel', {
    extend: 'Ext.data.Model',
    fields: [ 
		{name: 'text', type: 'string'},
		{name: 'leaf', type: 'boolean'},
		{name: 'survey_id', type: 'int'}
	]
});