Ext.define('Lougis.store.LouSurveyTreeStore', {
    extend: 'Ext.data.TreeStore',
	model: 'LouSurveyTreeModel',
    proxy: {
		type: 'ajax',
		url: '/run/lougis/lousurvey/surveyTreeJson/'
	},
	folderSort: false,
	root: null
});