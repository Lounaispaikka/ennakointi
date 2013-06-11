Ext.onReady(function(){
	
	//createMessageForm( type, 'newcommentform' );
	//luodaan shownewmsg funktiossa
});

function likeComment( MsgId ) {
	
	console.log("likeComment", MsgId);
	likeAjax( MsgId, 1 );
	return false;
	
}

function dislikeComment( MsgId ) {
	
	console.log("dislikeComment", MsgId);
	likeAjax( MsgId, -1 );
	return false;
	
}

function likeAjax( MsgId, LikeValue ) {
	
	
	Ext.Ajax.request({
		url: '/run/lougis/comment/likeMsg/',
		params: {
			msgid: MsgId,
			likeval: LikeValue
		},
		success: function(response) {
			var res = Ext.JSON.decode(response.responseText);
			var lbox = Ext.get('lbox'+res.comment.id);
			var spans = lbox.query('span');
			Ext.get(spans[0]).update(res.comment.likes);
			Ext.get(spans[1]).update(res.comment.dislikes);
			var as = lbox.query('a');
			Ext.get(as).set({ onclick: '' });
			lbox.addCls('clicked');
		}
	});
	console.log("likeAjax", MsgId, LikeValue);
	
}

function showReplyBox( ParentMsgId, this_page ) {
	
	Ext.Ajax.request({
		url: '/run/lougis/comment/replyBoxHtml/',
		params: {
			msgid: ParentMsgId
		},
		success: function(res) {
			var replybox = Ext.get('replybox'+ParentMsgId);
			replybox.update(res.responseText, true, function() {
				createMessageForm(/*'page', '1', */'replyform'+ParentMsgId, this_page, ParentMsgId);
			});
		}
	});
	
}

function closeReplyBox( ParentMsgId ) {
	
	var replybox = Ext.get('replybox'+ParentMsgId);
	replybox.update('');
	
}

//function showNewMsg( type, typeId ) {
function showNewMsg(/* type, typeId, */this_page, replyTo, topic_id ) {	
	
	if (typeof topic_id == 'undefined') topic_id = null;
	createMessageForm(/* type, typeId, */'newcommentform', this_page, replyTo, topic_id );
	var msgBox = Ext.get('newcomment');
	msgBox.animate({
		to: {
			opacity: 1	
		},
		listeners: {
			beforeanimate: function(anim) {
				anim.target.target.setStyle({
					display: 'block'	
				});
			}
		}
	});
	return false;
	
}
function CommentPage(page) {	
	
	createMessageForm( 'newcommentform', page );
	var msgBox = Ext.get('newcomment');
	msgBox.animate({
		to: {
			opacity: 1	
		},
		listeners: {
			beforeanimate: function(anim) {
				anim.target.target.setStyle({
					display: 'block'	
				});
			}
		}
	});
	return false;
	
}

function hideNewMsg(  ) {
	
	var msgBox = Ext.get('newcomment');
	msgBox.animate({
		to: {
			opacity: 0	
		},
		listeners: {
			afteranimate: function(anim) {
				anim.target.target.setStyle({
					display: 'none'	
				});
			}	
		}
	});
	jQuery('#newcommentform').empty();
	return false;
	
}

function createMessageForm( /*type, typeId, */targetId, this_page, replyTo, topic_id ) {
	console.log("buu");
	if ( typeof replyTo == 'undefined' ) replyTo = null;
	if ( typeof topic_id == 'undefined' ) topic_id = null;
	if ( typeof this_page == 'undefined' ) this_page = null;
	var formPanel = Ext.create('Ext.form.Panel', {
		title: null,
		border: 0,
		width: 400,
		modal: false,
		autoShow: true,
		url: '/run/lougis/comment/newcomment/',
		renderTo: targetId,
		buttonAlign: 'right',
		defaults: {
			xtype: 'textfield',
			labelWidth: 120,
			labelAlign: 'right',
			width: 350,
			minLengthText: "Tämän kentän vähimmäispituus on {0} merkkiä!",
			maxLengthText: "Tämän kentän maksimipituus on {0} merkkiä!"
		},
		items: [
			/*{
				name: 'type',
				xtype: 'hidden',
				value: type
			},
			{
				name: 'type_id',
				xtype: 'hidden',
				value: typeId
			},*/
			{
				name: 'this_page',
				xtype: 'hidden',
				value: this_page
			},
			{
				name: 'reply_to',
				xtype: 'hidden',
				value: replyTo
			},
			/*{
				name: 'comment[nick]',
				minLength: 2,
				maxLength: 200,
				fieldLabel: 'Nimi tai nimimerkki'
			},*/
			{
				xtype: 'textarea',
				name: 'comment[msg]',
				minLength: 2,
				fieldLabel: 'Viesti',
				labelAlign: 'top',
				height: 250,
				grow: true
			},
			{
				xtype: 'displayfield',
				fieldStyle: 'color:#888',
				margin: '25 0 0 15',
				value: 'Roskapostinesto: <br />Vastaa kysymykseen: Mikä on tämän vuoden vuosiluku?'
			},
			{
				name: 'comment[check]',
				minLength: 2,
				maxLength: 200,
				width: 50
			},
			/*{
				xtype: 'fieldcontainer',
				layout: 'hbox',
				fieldLabel: 'Vastaa kysymykseen',
				items: [
					{
						xtype: 'displayfield',
						value: 'Mikä on tämän vuoden vuosiluku?'
					},
					{
						xtype: 'textfield',
						name: 'comment[check]',
						width: 50,
						margin: '0 0 0 5'
					}
				]
			},*/
			
		],
		fbar: [
			{
				text: 'Lähetä',
				handler: function( btn ) {
					var form = btn.up('form').getForm();
					if ( form.isValid() ) {
						form.submit({
							success: function(form, action) {
								//window.open('?'+Math.round(Math.random()*1000)+'#cm'+action.result.comment.id, '_self');
								//window.open('#cm'+action.result.comment.id, '_self');'
								Ext.Msg.alert('Viesti lähetetty', action.result.msg);
								hideNewMsg();
							},
							failure: function(form, action) {
								Ext.Msg.alert('Virhe', action.result.msg);
							}
						});
					}
				}
			}
		],
		listeners: {
			beforerender: function(panel) {
				if (topic_id != null) {
					var topid = [
						{
							xtype: 'hiddenfield',
							name: 'comment[topic_id]',
							value: topic_id
						}
					];
					panel.insert(1, topid);
				}
				if ( replyTo != null ) {
					var extras = [
						{
							xtype: 'hiddenfield',
							name: 'comment[parent_id]',
							value: replyTo
						}
					];
				} else {
					var extras = [
						{
							name: 'comment[title]',
							minLength: 2,
							maxLength: 200,
							fieldLabel: 'Otsikko',
							labelAlign: 'top'
						}
					];
				}
				panel.insert(1, extras);
			},
			afterrender: function(panel) {
				if ( targetId == 'newcommentform' ) {
					//hideNewMsg();
				}
				
			}
		}
	});
	
}
