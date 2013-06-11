function showAdminMenu() {
	var adminMenu = Ext.create('Ext.toolbar.Toolbar', {
		id: 'adminMenu',
		title: 'Menu Test',
		renderTo: 'admin_menu',
		items: [ 
			{
				text: 'Valitse toimiala',
				menu: {
				},
			},
			{
				text: 'Lis&auml;&auml; aineistoa',
				menu: {
					xtype: 'menu',
					defaults: {
                        scale: 'small',
                        iconAlign: 'left'
					},
					items: [{
                        text: 'Lis&auml;&auml; sivu',
                        iconCls: 'edit'
                    },{
                        text: 'Lis&auml;&auml; tilasto',
                        iconCls: 'edit'
                    },{
						text: 'Lis&auml;&auml; tiedosto',
                        iconCls: 'edit'
                    },{
						text: 'Lis&auml;&auml; linkki',
                        iconCls: 'add',
                    }]	
				},
			},
			{
				text: 'Muokkaa aineistoa',
				menu: {
				},
			},
			{
				text: 'Keskustelut',
				menu: {
				},
			},
			'->',
			{
				text: 'Omat tiedot',
				menu: {
				},
			}
		]
	}).show();
}