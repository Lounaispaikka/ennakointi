Ext.define('Lougis.store.ChartTreeStore', {
    extend: 'Ext.data.TreeStore',
	model: 'ChartTreeModel',
    proxy: {
		type: 'ajax',
		url: '/run/lougis/charts/getChartsJson/'
	},
	folderSort: false,
	root: null
});