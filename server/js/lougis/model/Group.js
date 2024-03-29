Ext.define('Lougis.model.Group', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'int'},
        {name: 'name', type: 'string'},
        {name: 'date_created', type: 'string'},
        {name: 'public_joining', type: 'boolean'},
        {name: 'description', type: 'string'},
        {name: 'parent_id', type: 'int'},
		{name: 'is_admin', type: 'boolean'},
		{name: 'page_id', type: 'int'},
		{name: 'del_if_no_perm', type: 'boolean'}
    ],
    hasMany: {
        model: 'Lougis.model.User',
        name: 'users'
    },
    getForm: function() {
        var groupsStore = Ext.create('Lougis.store.Groups');
        var addUsersStore = Ext.create('Lougis.store.Users');
		var parentPageStore = Ext.create('Lougis.store.ParentPages');

        addUsersStore.on('load', function() {
            addUsersStore.filterBy(this.filterAddUsersStore, this);
        }, this);

        var basicItems = [{
            xtype: 'hiddenfield',
            name: 'id',
            value: this.get('id')
        },{
            xtype: 'hiddenfield',
            name: 'del_if_no_perm',
            value: false
        },{
            xtype: 'textfield',
            name: 'name',
            fieldLabel: 'Nimi',
            value: this.get('name'),
            allowBlank: false,
            listeners: {
                change: function(field, newValue) {
                    this.set('name', newValue)
                },
                scope: this
            }
        },{
            xtype: 'checkbox',
            name: 'public_joining',
            fieldLabel: 'Vapaa liittyminen',
            checked: this.get('public_joining'),
            listeners: {
                change: function(field, newValue) {
                    this.set('public_joining', newValue)
                },
                scope: this
            }
        },{
            xtype: 'textarea',
            name: 'description',
            fieldLabel: 'Kuvaus',
            value: this.get('description'),
            listeners: {
                change: function(field, newValue) {
                    this.set('description', newValue)
                },
                scope: this
            }
        },{
            xtype: 'combobox',
            name: 'parent_id',
            store: groupsStore,
            displayField: 'name',
            valueField: 'id',
            triggerAction: 'all',
            fieldLabel: 'Pääryhmä',
            value: this.get('parent_id'),
            listeners: {
                change: function(field, newValue) {
                    this.set('parent_id', newValue)
                },
                scope: this
            }
        },
		{
            xtype: 'checkbox',
            name: 'is_admin',
            fieldLabel: 'Admin-oikeudet (pääsy sisällönhallintaan)',
            checked: this.get('is_admin'),
            listeners: {
                change: function(field, newValue) {
                    this.set('is_admin', newValue)
                },
                scope: this
            }
        },
		,{
            xtype: 'combobox',
            name: 'page_id',
            store: parentPageStore,
            displayField: 'name',
            valueField: 'id',
            triggerAction: 'all',
            fieldLabel: 'Kuuluu sivuun',
            value: this.get('page_id'),
            listeners: {
                change: function(field, newValue) {
                    this.set('page_id', newValue)
                },
                scope: this
            }
        }];

        var userItems = [
			{
				xtype: 'grid',
				title: 'Lisää jäseniä',
				emptyText: 'Ei jäseniä',
				height: 300,
				autoScroll: true,
				flex: 1,
				margins: {
					top: 0,
					bottom: 6,
					left: 0,
					right: 2
				},
				store: addUsersStore,
				columns: [
					{header: "Nimi", dataIndex: "id", flex: 1, renderer: function(userId) {
							var user = Ext.getStore('userStore').getById(userId);
							return user.getFullnameWithEmail();

						}
					},
					{header: "Lisää", width: 50, align: 'center', renderer: function() {return  '<img src="/img/icons/16x16/add.png" class="clickable" />';}}
				],
				listeners: {
					scope: this,
					itemclick: function(view, record, item, index, event, options) {
						var user = new Lougis.model.User({
							id: record.get('id')
						});
						this.users().add(user);
						addUsersStore.filterBy(this.filterAddUsersStore, this);
					}
				}
			},
			{
				xtype: 'grid',
				title: 'Nykyiset jäsenet',
				store: this.users(),
				emptyText: 'Ei jäseniä',
				height: 300,
				autoScroll: true,
				margins: {
					top: 0,
					bottom: 6,
					left: 2,
					right: 0
				},
				flex: 1,
				columns: [
					{header: "Nimi", dataIndex: "id", flex: 1, renderer: function(userId) {
							var user = Ext.getStore('userStore').getById(userId);
							return user.getFullnameWithEmail();

						}
					},
					{header: "Ylläpitäjä", dataIndex: "isAdminOfAGroup",xtype: 'checkcolumn', width: 54},
					{header: "Poista", width: 50, align: 'center', renderer: function() {return  '<img src="/img/icons/16x16/delete.png" class="clickable" />';}}
				],
				listeners: {
					scope: this,
					itemclick: function(view, record, item, index, event, options) {
						Ext.Msg.confirm("Oletko varma?", "Haluatko varmasti poistaa käyttäjän ryhmästä?", function(button) {
							if(button == 'yes') this.users().remove(record);
							addUsersStore.filterBy(this.filterAddUsersStore, this);
						}, this);
					}
				}
			}
        ];

        var items = [{
            xtype: 'fieldset',
            title: 'Perustiedot',
            items: basicItems,
            defaults: {
                anchor: '100%'
            }
        },{
            xtype: 'fieldset',
            title: 'Hallitse jäseniä',
            layout: 'hbox',
            items: userItems,
            defaults: {
                anchor: '100%'
            }
        }];
        return items;
    },
    filterAddUsersStore: function(record, id) {
        return !this.users().getById(record.get('id'));
    }
});