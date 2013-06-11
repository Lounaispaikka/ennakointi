
//create new message...get user's name and trigger createMsgWindow() function
function createNewMsg() {
	var userName = Ext.Ajax.request({
		url: '/run/lougis/usersandgroups/getPhorumUserId/',
	/*	params: {
			user_id: <?php echo $_SESSION["user_id"];?>
		},*/
		success: function(response) {
			var res = Ext.JSON.decode(response.responseText);
			console.log(res);
			createMsgWindow(res);
		}
	});
}

function createMsgWindow(phorum_user_id) {
	if ( typeof phorum_user_id == 'undefined' ) phorum_user_id = 2;
	var forumMessageForm = Ext.create('Ext.form.Panel', {
		xtype: 'form',
		id: 'forumMessageForm',
		url: '/run/lougis/phorum_post/phorum_insert_lougis_post/',
		title: 'Kirjoita uusi viesti',
		fieldDefaults: {
				labelAlign: 'top',
				msgTarget: 'side',
				
			},
		cls: 'ext_window',
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
			},
			/*{	
				xtype: 'displayfield',
				fieldLabel: 'Kirjoittaja',
				name: 'name',
				value: user_id,
				anchor: '100%'
			},*/
			{	
				xtype: 'displayfield',
				fieldLabel: 'Phorum_user_id',
				name: 'msg[phorum_user_id]',
				value: phorum_user_id,
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
		],
		bodyPadding: 10,
		autoScroll: true,
		buttons: [{
				id: 'sendBtn',
				text: 'Lähetä',
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
					msgWindow.close();
			}
			}]
	});

	var msgWindow = Ext.create('Ext.window.Window', {
		title: 'Uusi viesti',
		width: 600,
		height: 400,
		autoShow: true,
		scrollable: true,
		modal: true,
		layout: 'fit',
		items: forumMessageForm
		
	});
}
