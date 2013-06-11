function createWindow(parent_page_id) {
	if ( typeof parent_page_id == 'undefined' ) return;
	
	var childBtn = Ext.create('Ext.Button', {
		text: 'Lis&auml;&auml; alasivu',
		icon: '/img/icons/32x32/page_add.png',
		cls: 'ext_clearBtns',
		scale: 'large',
		handler: function() {
			creatingWindow.removeAll();
			creatingWindow.add(newPagePanel);
		}
	});
	var chartBtn = Ext.create('Ext.Button', {
		text: 'Lis&auml;&auml; tilasto/indikaattori',
		icon: '/img/icons/32x32/statistics.png',
		cls: 'ext_clearBtns',
		scale: 'large',
		handler: function() {
			creatingWindow.removeAll();
			creatingWindow.add(newPagePanel);
		}
	});
	var docBtn = Ext.create('Ext.Button', {
		text: 'Lis&auml;&auml; dokumentti',
		icon: '/img/icons/32x32/blogs.png',
		cls: 'ext_clearBtns',
		scale: 'large',
		handler: function() {
			creatingWindow.removeAll();
			creatingWindow.add(newPagePanel);
		}
	});
	
	var creatingWindow = Ext.create('Ext.window.Window', {
		title: 'Lis&auml;&auml; aineisto',
		width: 620,
		height: 440,
		autoShow: true,
		scrollable: true,
		modal: true,
		layout: 'auto',
		items: [childBtn, chartBtn, docBtn]
	});
	
//}

//function createPageWindow(parent_page_id) {
	var newPagePanel = Ext.create('Ext.form.Panel', {
		xtype: 'form',
		id: 'newPagePanel',
		height: 400,
		width: 600,
		//url: '/run/lougis/phorum_post/phorum_insert_lougis_post/',
		title: 'Lis&auml;&auml; uusi alasivu',
		fieldDefaults: {
				labelAlign: 'top',
				msgTarget: 'side',
				
			},
		cls: 'ext_window',
		items: [
			{
				xtype: 'tabpanel',
				activeItem: 0,
				border: false,
				anchor: '100% 100%',
				
				//only field from an active tab are submitted if the following line is not present
				deferredRender: false,
				
				//tabs
				defaults:
					{
						layout:'form',
						labelWidth: 80,
						defaultType:'textfield',
						bodyStyle: 'padding: 5px',
						
						//when deferredRender:false, must not render tabs into display:none containers
						hideMode: 'offsets'
					},
					
					items: [
						{
							title:'tab1',
							autoscroll:true,
							defaults: {anchor:'-20'},
							
							//fields
							items: [
								{	
									xtype: 'displayfield',
									fieldLabel: 'page_id',
									name: 'msg[page_id]',
									//value: <?=$Pg->id?>,
									anchor: '100%'
								},
								{	
									xtype: 'displayfield',
									fieldLabel: 'thread_id',
									name: 'msg[thread]',
									//value: <?=$Thread_id?>,
									anchor: '100%'
								}
								/*{	
									xtype: 'displayfield',
									fieldLabel: 'Kirjoittaja',
									name: 'name',
									value: user_id,
									anchor: '100%'
								},*/
							
							]
						}, {
							
							title:'tab2',
							autoscroll:true,
							defaults: {anchor:'-20'},
							
							//fields
							items: [
								{	
									xtype: 'displayfield',
									fieldLabel: 'parent_page_id',
									name: 'msg[parent_page_id]',
									value: parent_page_id,
									anchor: '100%'
								},
								{	
									xtype: 'textfield',
									fieldLabel: 'Otsikko',
									name: 'msg[subject]',
									anchor: '100%'
								},
								{	
									xtype: 'textarea',
									fieldLabel: 'Viesti',
									name: 'msg[body]',
									height: 200,
									anchor: '100%'
								}
							]
						}
					]
			}
		],
		bodyPadding: 10,
		autoScroll: true,
		buttons: [{
				id: 'sendBtn',
				text: 'L&auml;het&auml;',
				handler: function(btn) {
					var form = btn.up('form').getForm();
					if ( form.isValid() ) {
						form.submit({
							success: function(form, action) {
								Ext.Msg.alert('Suksee');
								console.log(form);
							},
							failure: function(form, action) {
								Ext.Msg.alert('Virhe');
								console.log(form);
							}
						});
					}	
				}	
			},{
				text: 'Peruuta',
				id: 'closeBtn',
				handler: function() {
					creatingWindow.close();
			}
		}]
	});
}