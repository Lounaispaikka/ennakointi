Ext.define('Lougis.store.ParentPages', {
    extend: 'Ext.data.Store',
    model: 'Lougis.model.ParentPages',
	autoload: true,
	proxy: {
		type: 'ajax',
		url: '/run/lougis/cms/parentComboData/',
		reader: {
			type: 'json',
			root: null
		}
	}
});