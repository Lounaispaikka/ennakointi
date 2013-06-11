Ext.define('Lougis.view.Ennakointi', {
    extend: 'Lougis.view.Panel',
    alias: 'widget.ennakointi',
    id: 'LougisEnnakointiWidget',
	anchor: '100% 100%',
	border: 0,
    items: [],
    treeData: [],
    treeStoreRoot: [],
    initComponent: function() {
		this.callParent();
		console.log("1");
		this.cmsWindow = Ext.create('Ext.window.Window', {
			title: 'Lis&auml;&auml; aineisto',
			width: 620,
			height: 440,
			autoShow: true,
			scrollable: true,
			modal: true,
			layout: 'auto'
		});
                
		this.add(this.cmsWindow);
		/*
        this.parentPageStore = this.createParentPageStore();
		this.parentPageStore.load();
		this.navStore = this.createNavTreeStore('toimiala');
		this.navSidePanel = this.createNavSidePanel();
		this.cmsPanel.add(this.navSidePanel);
		
		this.cmsPageInfo = this.createCmsPageInfoPanel();
		this.cmsPageUserManagement = this.createCmsPageUserManagement();
		this.cmsContentEditor = this.createCmsContentEditorPanel();	
		this.cmsTabPanel = this.createCmsTabPanel();
		this.cmsPanel.add(this.cmsTabPanel);
		
		this.cmsColumnEditorPanel = this.createCmsColumnEditorPanel();
		this.cmsPanel.add(this.cmsColumnEditorPanel);
		
		//this.ennakointiStore = createEnnakointiGridStore('ennakointi');
		this.ennakointiSidePanel = this.createEnnakointiSidePanel();
		this.cmsPanel.add(this.ennakointiSidePanel);
		*/
		
    },
    
    createParentPageStore: function() {
    
    	return Ext.create('Ext.data.Store', {
			fields: ['title', 'page_id', 'level'],
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
                
    },
    
    createNavSidePanel: function() {
    
    	this.cmsSaveSortBtn = Ext.create('Ext.Button', {
			text: 'Tallenna järjestys',
			icon: '/img/icons/16x16/disk.png',
		    scope: this,
		    handler: function() {
		    	this.saveCmsTreeSort();
		    }
		});
		
		this.cmsChooseToimialaBtn = Ext.create('Ext.Button', {
			text: 'Valitse toimiala',
			icon: '/img/icons/16x16/page_edit.png',
			scope: this,
		    handler: function() {
		    	//this.createNewPageWin();
				alert("tättärätää");
		    }
		});
		
		this.cmsNewPageBtn = Ext.create('Ext.Button', {
			text: 'Lisää uusi ennakointi',
			icon: '/img/icons/16x16/page_edit.png',
			scope: this,
			disabled: true,
		    handler: function() {
		    	this.createNewPageWin();
		    }
		});
		
		this.navTreePanel = Ext.create('Ext.tree.Panel', {
			id: 'cmsNavTreePanel',
			store: this.navStore,
            rootVisible: false, 
			bodyPadding: '0 0 0 0',
			bodyBorder: false,
            scroll: 'both',
			layout: 'fit',
			buttonAlign: 'left',
			border: 0,
           /* viewConfig: {
                plugins: {
                    ptype: 'treeviewdragdrop'
                    }
                },
            */
			allowContainerDrop: false,
			listeners: {
				itemclick: {
					scope: this,
					fn: function( view, record, item, index ){
						if ( record.data.page_id !== null ) {
							this.currentRecord = record;
							this.loadPageToCMS( record.data.page_id );
						}
					}
				},
		        afterrender: {
		        	scope: this,
					fn: function( panel ) {
		        		panel.setHeight( document.body.clientHeight-188 );
		        	}
		        }
			}
		});
		
		this.navSidePanel = Ext.create('Ext.panel.Panel', {
			title: 'Ennakointi',
			region:'west',
			border: 0,
			layout: 'auto',
			id: 'cmsNavSidePanel',
			width: 250,
			split: true, //resizable (false=not)
			collapsible: false,   // make collapsible (false=not)
			buttons: [
				'->',this.cmsSaveSortBtn	
			],
			items: [this.navTreePanel],
			tbar: [
					this.cmsChooseToimialaBtn, this.cmsNewPageBtn
			]
		});
		
		return this.navSidePanel;
    
    },
    
    createCmsPageUserManagement: function() {
		
		if(!Ext.getStore('userStore')) Ext.create('Lougis.store.Users', {
                storeId: 'userStore'
            });
		
		this.userGrid = Ext.create('Ext.grid.Panel', {
			columns: [
                {header: "Sukunimi", dataIndex: "lastname", flex: 1},
				{header: "Etunimi", dataIndex: "firstname", flex: 1},
				{header: "Organisaatio", dataIndex: "organization", flex: 1},
				{header: "Sähköposti", dataIndex: "email", flex: 1}
			],
			store: 'userStore',
			multiSelect: true,
			emptyText: "Ei käyttäjiä",
			autoScroll: true,
			stripeRows: true,
			anchor: '100%',
			title: "Rekisteröityneet käyttäjät",
			viewConfig: {
				plugins: {
					ptype: 'gridviewdragdrop',
					dragGroup: 'userGridDDGroup',
					dropGroup: 'viewerGridDDGroup'
				},
				listeners: {
					drop: function(node, data, dropRec, dropPosition) {
						var dropOn = dropRec ? ' ' + dropPosition + ' ' + dropRec.get('email') : ' on empty view';
						Ext.example.msg("Drag from right to left", 'Dropped ' + data.records[0].get('email') + dropOn);
					}
				}
			}
		});
		/*var viewerStore = Ext.create('Ext.data.Store', {
			model: 'DataObject'
		});*/
		this.viewerGrid = Ext.create('Ext.grid.Panel', {
			columns: [
				{header: "Etunimi", dataIndex: "firstname", flex: 1},
                {header: "Sukunimi", dataIndex: "lastname", flex: 1},
                {header: "Sähköposti", dataIndex: "email", flex: 1},
				{header: "Organisaatio", dataIndex: "organization", flex: 1}
			],
			multiSelect: true,
			emptyText: "Ei käyttäjiä",
			autoScroll: true,
			anchor: '100%',
			title: "Ennakointi-adminit",
			//store: viewerStore,
			viewConfig: {
				plugins: {
					ptype: 'gridviewdragdrop',
					dragGroup: 'viewerGridDDGroup',
					dropGroup: 'userGridDDGroup'
				},
				listeners: {
					drop: function(node, data, dropRec, dropPosition) {
						var dropOn = dropRec ? ' ' + dropPosition + ' ' + dropRec.get('name') : ' on empty view';
						Ext.example.msg("Drag from left to right", 'Dropped ' + data.records[0].get('name') + dropOn);
					}
				}
			}
		});
		
		this.sendEmailBtn = Ext.create('Ext.Button', {
			text: 'Lisää uusi ennakointi',
			icon: '/img/icons/16x16/page_edit.png',
			scope: this,
			disabled: true,
		    handler: function() {
		    	this.createNewPageWin();
		    }
		});
		return Ext.create('Ext.Panel', {
			width: 650,
			height : 300,
			layout: {
				type: 'hbox',
				align: 'stretch',
				padding: 5
			},
			defaults: { flex : 1 }, //auto stretch
			items: [this.userGrid, this.viewerGrid],
			title: "Käyttäjät"
		});

	
	},
	
	createCmsPageInfoPanel: function() {
    
   		return Ext.create('Ext.form.Panel', {
			url: '/run/lougis/cms/savePageInfo/',
			id: 'cmsPageInfoPanel',
			itemId: 'cmsPageInfoTab',
			title: 'Perustiedot',
			fieldDefaults: {
				labelWidth: 150
			},
			defaultType: 'textfield',
			collapsible: false,
			collapsed: false,
			scope: this,
			buttonAlign: 'left',
			items: this.getPageInfoFormItems(),
			buttons: [
    			Ext.create('Ext.Button', {
					text: 'Tallenna perustiedot',
					icon: '/img/icons/16x16/disk.png',
					width: 150,
					scope: this,
				    handler: function() {
				    
				        var form = this.cmsPageInfo.getForm();
                        if( form.isValid() ) {
                        	//form.findField('cmsPageInfoExtra1').setValue(CKEDITOR.instances.cmsPageInfoFieldExtra1.getData());
							form.submit({
								scope: this,
								success: function(form, action) {
									this.reloadNavTreeStore();
									
                    				this.currentRecord.set('text', form.findField('cms_page[nav_name]').value);
                    				
									Ext.Msg.alert('Tallennus onnistui', action.result.msg);
									this.parentPageStore.load();
									this.parentPageStore.load();
								},
								failure: function(form, action) {
									Ext.Msg.alert('Virhe!', action.result.msg);
								}
							});
                        }
                        else {Ext.Msg.alert("Virhe lomakkeen tiedoissa", "Täytä kaikki lomakkeen kentät kunnollisilla arvoilla.");}
				        
				    }
				}),
				'->',
				Ext.create('Ext.Button', {
					text: 'Poista sivu',
					icon: '/img/icons/16x16/delete.png',
					width: 100,
					scope: this,
				    handler: function() {
				    	if ( confirm("Haluatko varmasti poistaa tämän sivun? Toimintoa ei voi peruuttaa.") ) {
				    		
					    	Ext.Ajax.request({
								url: '/run/lougis/cms/deletePage/',
								scope: this,
								params: {
									page_id: this.cmsPageInfo.getForm().getFieldValues().page_id
								},
								success: function( xhr ){
									var res = Ext.JSON.decode(xhr.responseText);
									if ( res.success ) {
										//this.reloadNavTreeStore();
										
										this.currentRecord.remove();
										this.cmsTabPanel.disable();
										Ext.Msg.alert('Sivu poistettu', res.msg);
										this.parentPageStore.load();
										this.parentPageStore.load();
									} else {
										Ext.Msg.alert('Virhe!', res.msg);
									}
								}
							});
				    		
				        }
				        
				    }
				})
			]
    	});
    
    },
    
    createCmsContentEditorPanel: function() {
		CKEDITOR.stylesSet.add( 'my_styles',
			[
				// Block-level styles
				{ name : 'Blue Title', element : 'h2', styles : { 'color' : 'Blue' } },
				{ name : 'Red Title' , element : 'h3', styles : { 'color' : 'Red' } },
			 
				// Inline styles
				{ name : 'CSS Style', element : 'span', attributes : { 'class' : 'my_style' } },
				{ name : 'Marker: Yellow', element : 'span', styles : { 'background-color' : 'Yellow' } }
			]
		);
    	this.cmsCKEditorField = Ext.create('Ext.form.field.TextArea', {
                id: 'cmsCKEditorTextArea',
                inputId: 'cmsCKEditorField',
                xtype: 'textarea',
                scope: this,
                anchor: '100% 100%',
                margin: '0 0 0 0',
                name: 'cmsContentData'
        });
        this.cmsCKEditorField.on('afterrender', function( container, layout ){

                var editoHeight = Ext.getBody().getHeight()-290;
                CKEDITOR.replace( 'cmsCKEditorField', {
                        toolbar: 'Lougis',
                        language: 'fi',
                        //config.stylesSet = 'my_styles',
                        width: 610,
                        height: editoHeight
                        //,height: 500
                });

        });

        return Ext.create('Ext.form.Panel', {
                itemId: 'cmsContentEditorTab',
                id: 'cmsContentEditorPanel',
                collapsible: false,
                collapsed: false,
                title: 'Sisältö',
                anchor: '100% 100%',
                autoScroll: true,
                bodyPadding: '0 0 0 0',
                padding: '0 0 0 0',
                buttonAlign: 'center',
                defaultType: 'textarea',
                items: [ 
                        this.cmsCKEditorField 
                ],
                buttons: [
                        {
                                text: 'Tallenna',
                                icon: '/img/icons/16x16/disk.png',
                                width: 150,
                                scope: this,
                                handler: this.saveContentAndColumn

                        }
                ]
        });

    
    },
    
    createCmsTabPanel: function (){
    
    	return Ext.create('Ext.tab.Panel', {
				id: 'cmsTabPanel',
				region: 'center',
				disabled: true,
				anchor: '100% 100%',
				defaults: {
					bodyStyle: 'padding: 15px'
				},
				/*
				layout: 'accordion',
				layoutConfig: {
					titleCollapse: true,
					animate: true
				},
				*/
				activeTab: 1,
				items: [ this.cmsContentEditor, this.cmsPageInfo, this.cmsPageUserManagement ]
		});
    
    },
    
    createCmsColumnEditorPanel: function() {
    
    	this.cmsColumnEditorField = Ext.create('Ext.form.field.TextArea', {
			id: 'cmsColumnEditorTextArea',
			inputId: 'cmsColumnEditorField',
			xtype: 'textarea',
            scope: this,
			anchor: '100% 100%',
			margin: '0 0 0 0',
			name: 'cmsColumnData'
		});
		
		this.cmsColumnEditorField.on('afterrender', function( container, layout ){
			
			var editoHeight = Ext.getBody().getHeight()-290;
			CKEDITOR.replace( 'cmsColumnEditorField', {
				toolbar: 'LougisColumn',
				language: 'fi',
                                width: 220,
				height: editoHeight,
				resize_enabled: false
				//,height: 500
			});
			CKEDITOR.on('instanceReady', function( evt ){
				var editor = CKEDITOR.instances.cmsColumnEditorField;
				//var editor = evt.editor;
				if (CKEDITOR.env.webkit) {
			        var iframe = document.getElementById('cke_contents_' + editor.name).firstChild;
			        iframe.style.display = 'none';
			        iframe.style.display = 'block';
			    }
			});
		});
		
		
		return Ext.create('Ext.form.Panel', {
				id: 'cmsColumnEditorPanel',
				itemId: 'cmsColumnEditorPanel',
				split: true,
				collapsible: false,
				collapsed: false,
				disabled: true,
				region: 'east',
				width: 211,
				title: 'Linkkipalsta',
				//anchor: '210 100%',
				autoScroll: true,
				bodyPadding: '0 0 0 0',
				padding: '0 0 0 0',
				buttonAlign: 'center',
				defaultType: 'textarea',
				items: [ 
					this.cmsColumnEditorField 
				],
				buttons: [
	    			Ext.create('Ext.Button', {
						text: 'Tallenna',
						icon: '/img/icons/16x16/disk.png',
						scope: this,
					    handler: this.saveContentAndColumn
					})
    			]
		});

    
    },
	
	createEnnakointiSidePanel: function() {
		/*
		this.ennakointiTreePanel = Ext.create('Ext.tree.Panel', {
			id: 'ennakointiNavTreePanel',
			store: this.ennakointiStore,
            rootVisible: false, 
			bodyPadding: '0 0 0 0',
			bodyBorder: false,
            scroll: 'both',
			layout: 'fit',
			buttonAlign: 'left',
			border: 0,
            allowContainerDrop: false,
			listeners: {
				itemclick: {
					scope: this,
					fn: function( view, record, item, index ){
						if ( record.data.page_id !== null ) {
							this.currentRecord = record;
							//this.loadPageToCMS( record.data.page_id );
						}
					}
				},
		        afterrender: {
		        	scope: this,
					fn: function( panel ) {
		        		//panel.setHeight( document.body.clientHeight-188 );
		        	}
		        }
			}
		});
		*/
		this.ennakointiSidePanel = Ext.create('Ext.panel.Panel', {
			title: 'Ennakointi',
			region:'center',
			border: 0,
			//layout: 'auto',
			id: 'ennakointiSidePanel',
			width: 250,
			split: true, //resizable (false=not)
			collapsible: false   // make collapsible (false=not)
			//items: [this.ennakointiTreePanel]
		});
		
		return this.ennakointiSidePanel;
	},
    
    saveContentAndColumn: function(){
    	
    	Ext.Ajax.request({
			url: '/run/lougis/cms/savePageContent/',
			scope: this,
			params: {
				page_id: this.cmsPageInfo.getForm().getFieldValues().page_id,
				new_content: CKEDITOR.instances.cmsCKEditorField.getData(),
				new_column: CKEDITOR.instances.cmsColumnEditorField.getData()
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				if ( res.success ) {
					Ext.Msg.alert('Tallennus onnistui', res.msg);
				} else {
					Ext.Msg.alert('Virhe!', res.msg);
				}
			}
		});
    	
    },
    
    reloadNavTreeStore: function( ) {
    	this.navTreePanel.setLoading(true);
    	this.navStore.getRootNode().removeAll();
    	this.navStore.load();
    	this.navTreePanel.setLoading(false);
    
    },
    
    createNavTreeStore: function(pageType) {
    
    	var newStore = Ext.create('Ext.data.TreeStore', {
			fields: [ 'text', 'page_id', 'page_type' ],
			proxy: {
				type: 'ajax',
				url: '/run/lougis/cms/navTreeJson/',
				extraParams: {  
					'Page_type': pageType 
				}  
			},
			folderSort: false,
			listeners: {
				load: {
					scope: this,
					fn: function(store, root, records) {
						this.treeStoreRecords = records;
					}
				}
			}
		});
		
		return newStore;
    
    },
		    
    createEnnakointiGridStore: function(pageType) {
    
    	var myData = [
			['3m Co',1,'ennakointi'],
			['Alcoa Inc',2,'ennakointi'],
			['Boeing Co.',3,'ennakointi'],
			['Hewlett-Packard Co.',4,'ennakointi'],
			['Wal-Mart Stores, Inc.',5,'ennakointi']
		];
		
		var arrayStore = Ext.create('Ext.data.ArrayStore', {
			fields: [ 
				{name: 'text', type: 'string'}, 
				{name: 'page_id', type: 'int'},
				{name: 'page_type', type: 'string'}
			],
			id: 'ennakointiArrayStore',
			/*proxy: {
				type: 'ajax',
				url: '/run/lougis/cms/navTreeJson/',
				extraParams: {  
					'Page_type': pageType 
				}  
			},*/
			data: myData,
			autoDestroy: true,
			idIndex: 0
			/*listeners: {
				load: {
					scope: this,
					fn: function(store, root, records) {
						this.arrayStoreRecords = records;
					}
				}
			}*/
		});
		
		return arrayStore;
    
    },
    
    loadPageToCMS: function( pageId ) {
    	
    	if ( this.cmsTabPanel.disabled ) {
    		this.cmsTabPanel.enable();
    	}
    	if ( this.cmsColumnEditorPanel.disabled ) {
    		this.cmsColumnEditorPanel.enable();
    	}
		Ext.Ajax.request({
			url: '/run/lougis/cms/getPageJson/',
			scope: this,
			params: {
				page_id: pageId
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				if ( res.success ) {
					this.updateCmsForms( res.page_id, res.page, res.content, res.content_column );
                                } else {
					Ext.Msg.alert('Virhe!', res.msg);
				}
			}
		});
    	
    },
    
    updateCmsForms: function( pageId, pageData, contentData, columnData ) {
    	this.cmsPageInfo.getForm().setValues( pageData );
    	/*if ( typeof CKEDITOR.instances.cmsPageInfoFieldExtra1 !== "undefined" ) {
    		CKEDITOR.instances.cmsPageInfoFieldExtra1.setData( pageData['cms_page[extra1]'] );
                console.log("3.if");
  	} else {
                Ext.getCmp('cmsPageInfoExtra1').setValue( pageData['cms_page[extra1]'] );
                
                console.log("3.else");
    	}*/
    	
    	if ( contentData === null ) {contentData = "";}
    	if ( typeof CKEDITOR.instances.cmsCKEditorField !== "undefined" ) {
    		CKEDITOR.instances.cmsCKEditorField.setData( contentData );
                this.cmsPageInfo.updateLayout();
    	} else {
    		Ext.getCmp('cmsCKEditorTextArea').setValue( contentData );
                this.cmsPageInfo.updateLayout();
          	}
    	
    	if ( typeof CKEDITOR.instances.cmsColumnEditorField !== "undefined" ) {
    		CKEDITOR.instances.cmsColumnEditorField.setData( columnData );		
	        var iframe = document.getElementById('cke_contents_' + CKEDITOR.instances.cmsColumnEditorField.name).firstChild;
	        iframe.style.display = 'none';
	        iframe.style.display = 'block';
    		
    	} else {
    		Ext.getCmp('cmsColumnEditorTextArea').setValue( columnData );
    	} 	
    },
    
    getPageInfoFormItems: function( pageData ) {
    	
    	if ( typeof pageData === 'undefined' ) {
    		pageData = {
    			page_id: null,
    			title: null,
    			nav_name: null,
    			url_name: null,
    			in_navigation: true,
    			published: true,
    			extra1: null
    		};   		
    	}
    	
    	this.parentPageCombo = Ext.create('Ext.form.ComboBox', {
			fieldLabel: 'Ylempi sivu',
			scope: this,
			id: 'cmsParentPageCombo',
		    name: 'cms_page[parent_id]',
			store: this.parentPageStore,
			queryMode: 'local',
			width: 600,
			displayField: 'title',
			valueField: 'page_id',
		});
		/*
		this.pageTypeCombo = Ext.create('Ext.form.ComboBox', {
		    fieldLabel: 'Sivun tyyppi',
		    scope: this,
		    name: 'cms_page[page_type]',
		    store: Ext.create('Ext.data.Store', {
			    fields: ['page_type', 'title'],
			    data : [
			        {"page_type": null, "title": "Oletus"},
			        {"page_type": "strategia", "title":"Strateginen tavoite"},
			        {"page_type": "ohjelma", "title":"Ohjelmatavoite"},
			        {"page_type": "toimenpide", "title":"Toimenpide"}
			    ]
			}),
		    queryMode: 'local',
		    displayField: 'title',
		    valueField: 'page_type'
		});
    	*/
		var cmsPageInfoItems = [
		
				{
					xtype: 'fieldset',
					title: 'Toimialan perustiedot',
					items: [
				        {
				            name: 'page_id',
				            xtype: 'hidden',
				            value: pageData.page_id
				        },
				        {
				            name: 'cms_page[page_id]',
				            xtype: 'hidden',
				            value: pageData.page_id
				        },
						{ //Ennakointi-sivun tyyppi hard-coded
				            name: 'cms_page[page_type]',
				            xtype: 'hidden',
				            value: 'ennakointi'
				        },
						{ //Parent hard-coded
				            name: 'cms_page[parent_id]',
				            xtype: 'hidden',
				            value: '2'
				        },
				        {
				        	xtype: 'displayfield',
				        	id: 'cmsPageInfoFieldTitle',
				            fieldLabel: 'Toimiala',
				            name: 'cms_page[title]',
				            width: 450,
				            maxLength: 250,
				            value: pageData.title
                        },
				        {
				        	xtype: 'displayfield',
				        	id: 'cmsPageInfoFieldDescription',
				            fieldLabel: 'Lyhyt kuvaus',
				            name: 'cms_page[description]',
				            width: 600,
				            height: 50,
				            maxLength: 250,
				            value: pageData.description
				        },
				        {
				            xtype: 'checkbox',
				            fieldLabel: 'Navigaatiossa',
				            name      : 'cms_page[visible]',
			                inputValue: 'true',
							disabled: 'true',
			                checked	  : pageData.in_navigation
				        },
				        {
				            xtype: 'checkbox',
				            fieldLabel: 'Julkaistu',
				            name      : 'cms_page[published]',
			                inputValue: 'true',
							disabled: 'true',
			                checked	  : pageData.published
				        },
						{
				        	xtype: 'displayfield',
				        	id: 'cmsPageInfoFieldTitle',
				            fieldLabel: 'Toimiala-adminit',
				            name: 'cms_page[adminit]',
				            width: 450,
				            maxLength: 250,
				            value: 'Matti Meikäläinen matti.meikalainen@lounaispaikka.fi, Informaatio Teknologia i.t@lounaispaikka.fi'
                        }
					
					]
				}
		];
		/*
		if ( siteId == 'everkosto' ) {
		
			var everkostoPageInfo = {
				xtype: 'fieldset',
				title: 'E-verkostosivun tyyppi',
				items: [
				    this.pageTypeCombo,
			        {
			        	xtype: 'textarea',
			        	id: 'cmsPageInfoExtra1',
						inputId: 'cmsPageInfoFieldExtra1',
			            fieldLabel: 'E-verkostosivun kuvaus',
			            name: 'cms_page[extra1]',
			            width: 600,
			            value: pageData.extra1,
			            listeners: {
			            	afterrender: {
			            		scope: this,
			            		fn: function( container, layout ) {
			            			CKEDITOR.replace( 'cmsPageInfoFieldExtra1', {
										toolbar: 'LougisCmsExtra',
										language: 'fi',
										width: 350,
										height: 150
									});
									CKEDITOR.on('instanceReady', function( evt ){
										var editor = CKEDITOR.instances.cmsPageInfoFieldExtra1;
										if (CKEDITOR.env.webkit) {
									        var iframe = document.getElementById('cke_contents_' + editor.name).firstChild;
									        iframe.style.display = 'none';
									        iframe.style.display = 'block';
									    }
									});
			            		} 
			            	}
			            }
			        }
				] 
			};
			cmsPageInfoItems.push( everkostoPageInfo );
		
		}*/
		
		return cmsPageInfoItems;
    	
    },
    
    updatePageInfoFields: function( inputValue, pageId ) {
    	
    	if ( pageId === null ) {
    	
    		var form = Ext.getCmp('cmsNewPageFormPanel').getForm();
    	
    	}
    	
    
    },
    
    createNewPageWin: function() {
    
        var window = Ext.create('Ext.window.Window', {
            width: 730,
            height: 330,
            modal: true,
            title: "Lisää uusi ennakointi",
            layout: 'fit',
            items: []
        });
        
    	var formPanel = Ext.create('Ext.form.Panel', {
    		id: 'cmsNewPageFormPanel',
    		url: '/run/lougis/cms/createNewPage/',
			fieldDefaults: {
				labelWidth: 150
			},
            items: this.getPageInfoFormItems(),
            bodyStyle: 'padding: 5px',
            buttonAlign: 'left',
            scope: this,
            buttons: [
                Ext.create('Ext.Button', {
                    text: 'Peruuta',
                    icon: '/img/icons/16x16/delete.png',
                    handler: function() {
                    	formPanel.getForm().findField('cms_page[title]').clearListeners();
						window.close();	
                    },
                    scope: this
                }),
                '->',
                Ext.create('Ext.Button', {
                    text: 'Tallenna',
                    icon: '/img/icons/16x16/disk.png',
                    handler: function() {
                        var form = formPanel.getForm();
                        if( form.isValid() ) {
							form.submit({
								scope: this,
								success: function(form, action) {
									this.loadPageToCMS(action.result.page_id);
									this.reloadNavTreeStore();
									Ext.Msg.alert('Tallennus onnistui', action.result.msg);
									window.close();
									this.parentPageStore.load();
									//this.parentPageStore.load();
								},
								failure: function(form, action) {
									Ext.Msg.alert('Virhe!', action.result.msg);
								}
							});
                        }
                        else { Ext.Msg.alert("Virhe lomakkeen tiedoissa", "Täytä kaikki lomakkeen kentät kunnollisilla arvoilla.");}

                    },
                    scope: this
                })
            ]
        });
        
        var form = formPanel.getForm();
        var navNameField = form.findField('cms_page[nav_name]');
        var urlNameField = form.findField('cms_page[url_name]');
        form.findField('cms_page[title]').on('keyup', function( el ){
        	var title = el.value;
        	if ( title !== null && title.length > 0 ) {
	        	if ( title.length < 50 ) {navNameField.setValue(title);}
	        	if ( title.length < 50 ) {urlNameField.setValue(this.cmsWebSafeString(title));}
        	}
        }, this);
        
		window.add( formPanel );
        window.show();
    
    },
    
    cmsWebSafeString: function( input ) {
    	
    	input = input.toLowerCase();
    	input = input.replace('ä', 'a', 'gi');
    	input = input.replace('å', 'a', 'gi');
    	input = input.replace('ö', 'o', 'gi');
    	input = input.replace(/[^a-zA-Z0-9\s]+/g,'');
    	input = input.replace(' ', '-', 'gi');
    	return input;
    },
    
    saveCmsTreeSort: function() {
    
    	this.treeData = [];
		this.treeData = this._recurseCmsTree( this.navStore.getRootNode() );
		
		Ext.Ajax.request({
			url: '/run/lougis/cms/saveTreeSort/',
			scope: this,
			params: {
				tree_data: Ext.JSON.encode(this.treeData)
			},
			success: function( xhr ){
				var res = Ext.JSON.decode(xhr.responseText);
				if ( res.success ) {
					Ext.Msg.alert('Tallennus onnistui', res.msg);
				} else {
					Ext.Msg.alert('Virhe!', res.msg);
				}
			}
		});
    
    },
    
    _recurseCmsTree: function( leaf ) {
    	var branch = [];
    	leaf.eachChild(function( kid ){
    		kid.data.children = [];
			if ( kid.childNodes.length > 0 ) {kid.data.children = this._recurseCmsTree(kid);}
			branch.push(kid.data);
    	}, this);
    	return branch;
    	
    }
    
});