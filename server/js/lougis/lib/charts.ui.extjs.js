Ext.onReady(function () {
	
});
function startNewChart(parent_id) {

	this.addNewDataPanel = createNewDataPanel(parent_id);
	
	this.newChartWin = Ext.create('widget.window', {
		id: 'chartWin',
		title: 'Luo uusi tilasto',
		closable: true,
		minWidth: 350,
		//height: 500,
		modal: true,
		layout: 'fit',
		autoScroll: 'auto',
		//bodyStyle: 'padding: 5px;',
		items: [ 
			this.addNewDataPanel
		]
	});
	console.log('startNewChart');
	
	if (this.newChartWin.isVisible()) {
		this.newChartWin.close();
	} else {
		this.newChartWin.show();
	}

}

function createNewDataPanel(parent_id) {

	return Ext.create('Ext.form.Panel', {
		id: 'newDataForm',
		itemId: 'newDataFormPanel',
		url: '/run/lougis/charts/uploadData/',
		bodyPadding: 10,
		border: 0,
		autoScroll: true,
		frame: false,
		items: [
			{
				xtype: 'filefield',
				name: 'datafile',
				fieldLabel: 'Datatiedosto',
				emptyText: 'taulukkotiedosto.csv',
				width: 400,
				allowBlank: false,
				buttonText: 'Valitse tiedosto'
			},
			{
				xtype: 'displayfield',
				value: 'Valitse koneeltasi ladattava CSV-tiedosto ja klikkaa "Seuraava &raquo;".'	
			}
		],
		fbar: [
			{
				text: 'Seuraava &raquo;',
				scope: this,
				handler: function(button) {
				
					var form = button.up('form').getForm();
					this.newChartWin.setLoading(true);
					form.submit({
						scope: this,
						success: function(form, action) {
							var res = Ext.JSON.decode(action.response.responseText);
							if ( res.success ) {
								createNewDataInfo( res.chart, parent_id );
								console.log("pid", parent_id);
							} else {
								newChartWin.setLoading(false);
								Ext.Msg.alert('Virhe!', res.msg);
							}
						},
						failure: function(form, action) {
							Ext.Msg.alert('Virhe!', action.result.msg);
						}
					});
				}
			}
		]
		
	});
	console.log('createNewPanel');
}

function createNewDataInfo( chartObj, parent_id ) {
	    
	this.newChartWin.removeAll();
	var chartFieldsFieldset = getChartFieldsFieldset( chartObj.data.fields );
	var newDataInfoForm = Ext.create('Ext.form.Panel', {
		id: 'newDataInfoForm',
		itemId: 'newDataInfoFormPanel',
		url: '/run/lougis/charts/saveChartInfo/',
		bodyPadding: '10 10 10 10',
		border: 0,
		autoScroll: true,
		buttonAlign: 'left',
		frame: false,
		items: [
			{
				xtype: 'hiddenfield',
				name: 'chart[id]',
				value: chartObj.id
			},
			{
				xtype: 'textfield',
				fieldLabel: 'Tilaston otsikko',
				name: 'chart[title]',
				width: 350,
				value: chartObj.title	
			},
			{
				xtype: 'hiddenfield',
				name: 'chart[parent[id]',
				value: parent_id
			},
			chartFieldsFieldset
		],
		fbar: [
			{
				text: '&laquo; Takaisin',
				scope: this,
				handler: function(button) {
					this.newChartWin.setLoading(true);
					this.addNewDataPanel = createNewDataPanel();
					this.newChartWin.removeAll(true);
					this.newChartWin.add(addNewDataPanel);
					this.newChartWin.setLoading(false);
				}
			}, 
			'->',
			{
				text: 'Seuraava &raquo;',
				scope: this,
				handler: function(button) {
				
					var form = button.up('form').getForm();
					this.newChartWin.setLoading(true);
					
					form.submit({
						scope: this,
						success: function(form, action) {
							var res = Ext.JSON.decode(action.response.responseText);
							//this.reloadChartTreeStore();
							setChartToEditor( res.chart );
							//this.newChartWin.close();
							//newChartWin.removeAll(true); //tyhjennet‰‰n ikkuna
							
						},
						failure: function(form, action) {
							Ext.Msg.alert('Virhe!', action.result.msg);
							this.newChartWin.setLoading(false);
						}
					});
				}
			}
		]
		
	});
	this.newChartWin.add( newDataInfoForm );
	this.newChartWin.setLoading(false);
	console.log('createNewDataInfo');
}

function getChartFieldsFieldset( fieldsArray ) {
    
	var fieldTypesStoreData = [];
	for(var typekey in this.fieldTypes) {
		var drow = { datatype: typekey, title: this.fieldTypes[typekey] }
		fieldTypesStoreData.push( drow );
	}
	var fieldTypesStore = Ext.create('Ext.data.Store', {
		fields: ['datatype', 'title'],
		data: fieldTypesStoreData
	});
	
	var fieldEditors = [{
		layout: 'hbox',
		border: 0,
		items: [
			{
				xtype: 'displayfield',
				value: 'Sarake',
				width: 50,
				fieldStyle: 'color:#666;'
			},
			{
				xtype: 'displayfield',
				value: 'Otsikko',
				width: 250,
				margin: '0 0 0 10'
			},
			{
				xtype: 'displayfield',
				value: 'Tietotyyppi',
				margin: '0 0 0 20'
			}
		]
	}];
	Ext.each(fieldsArray, function( field, idx ){
		var row = {
			layout: 'hbox',
			border: 0,
			items: [
				{
					xtype: 'displayfield',
					value: 'Kentt‰ '+(idx+1),
					width: 50,
					fieldStyle: 'color:#666;'
				},
				{
					xtype: 'textfield',
					name: 'fields['+idx+'][name]',
					value: field.name,
					width: 250,
					margin: '0 0 0 10'
				},
				{
					xtype: 'combo',
					margin: '0 0 0 20',
					name: 'fields['+idx+'][type]',
					store: fieldTypesStore,
					queryMode: 'local',
					displayField: 'title',
					valueField: 'datatype',
					value: field.type
				},
				{
					xtype: 'hidden',
					name: 'fields['+idx+'][dataindex]',
					value: field.dataindex
				}
			]
		}
		fieldEditors.push(row);
	}, this);
	var info = {
		xtype: 'fieldcontainer',
		margin: '10 0 10 70',
		defaults: {
			labelWidth: 100	
		},
		items: [
			{
				xtype: 'displayfield',
				fieldLabel: '<b>Tietotyypit</b>'
			},
			{
				xtype: 'displayfield',
				fieldLabel: 'Teksti',
				value: 'Vapaa teksti. Tekstin pituutta ei ole rajoitettu.'
			},
			{
				xtype: 'displayfield',
				fieldLabel: 'Kokonaisluku',
				value: 'Numero ilman desimaaleja. Esim. 1, 2, 3'
			},
			{
				xtype: 'displayfield',
				fieldLabel: 'Desimaaliluku',
				value: 'Numero sis‰lt‰en desimaaleja. Esim. 1.0, 2.4, 3.6'
			},
			{
				xtype: 'displayfield',
				fieldLabel: 'Totuusarvo',
				value: 'Totuusarvo kyll‰/ei'
			},
			{
				xtype: 'displayfield',
				fieldLabel: 'P‰iv‰m‰‰r‰',
				value: 'P‰iv‰ys, esim. "7.6.2012" tai "7.6.2012 15:30:00"'
			}
		]
	};
	fieldEditors.push(info);
	var fieldset = {
		xtype: 'fieldset',
		title: 'Taulukon sarakkeet',
		padding: '10 10 10 10',
		items: fieldEditors
	}
	console.log('getChartFieldsFieldset');
	
	return fieldset;
	
}

function setChartToEditor( chartObj ) {
	
	newChartWin.removeAll(true);
	var chartEditor = createChartEditor(chartObj);
	newChartWin.add(chartEditor);
	newChartWin.setLoading(false);
	
}

function createChartEditor( chartObj ) {

	var infoForm = createInfoForm( chartObj );
	var dataGrid = createDataGrid( chartObj );
	//var chartForm = createChartBuilder( chartObj );
	var iframeForm = createIframeForm( chartObj );
	
	if ( chartObj.request != null ) {
		 
		Ext.each(chartObj.config.series, function(serie, idx) {
			addCurrentChartSeries( chartObj );
		}, this);
		chartForm.getForm().setValues( chartObj.request );
		
	}
	
	var chartEditor = Ext.create('Ext.tab.Panel', {
		xtype: 'tabpanel',
		region: 'center',
		title: 'Tilasto',
		activeTab: 0,
		items: [ infoForm, dataGrid, /*chartForm, */iframeForm ]
	});
	console.log(chartEditor);
	return chartEditor;
	
}

function createInfoForm( chartObj ) {
	
	var chartFieldsFieldset = getChartFieldsFieldset( chartObj.data.fields );
	var infoForm = {
		xtype: 'form',
		title: 'Perustiedot',
		id: 'chartInfoForm',
		url: '/run/lougis/charts/saveChartInfo/',
		bodyPadding: 10,
		border: 0,
		autoScroll: true,
		buttonAlign: 'right',
		frame: false,
		items: [
			{
				xtype: 'hiddenfield',
				name: 'chart[id]',
				value: chartObj.id	
			},
			{
				xtype: 'hiddenfield',
				name: 'chart[page_id]',
				value: chartObj.page_id	
			},
			{
				xtype: 'checkboxfield',
				fieldLabel: 'Julkaise tilasto indikaattorina',
				name: 'chart[published]',
				inputValue: 'true',
						checked	  : chartObj.published	
			},
			{
				xtype: 'textfield',
				fieldLabel: 'Tilaston otsikko',
				name: 'chart[title]',
				width: 350,
				value: chartObj.title	
			},
						 
			{
				xtype: 'textarea',
				fieldLabel: 'Tilaston lyhyt kuvaus (enint‰‰n 255 merkki‰)',
				inputId: 'chart_short_description',
				name: 'chart[short_description]',
						width: 530,
						height: 80,
						maxLength: 255,
						maxLengthText: 'Virhe: Pituus saa olla enint‰‰n 255 merkki‰',
				value: chartObj.short_description
				
			},		        
					{
				xtype: 'textarea',
				fieldLabel: 'Tilaston kuvaus',
				inputId: 'chart_description',
				name: 'chart[description]',
				value: chartObj.description,
				listeners: {
					/*afterrender: function() {
						 if ( typeof CKEDITOR.instances.chart_description != 'undefined' ) CKEDITOR.instances.chart_description.destroy( true );
						CKEDITOR.replace( 'chart_description', { toolbar: 'Lougis',language: 'fi',width: 530,height: 250 });
					}*/
				}
			},
			chartFieldsFieldset,
			{
				xtype: 'button',
				text: 'Poista tilasto',
				icon: '/img/icons/16x16/delete.png',
				scope: this,
				handler: function() {
					var msg = 'Haluatko varmasti poistaa koko tilaston? T‰t‰ toimintoa ei voi peruuttaa!';
					Ext.Msg.confirm('Poista tilasto', msg, function(button){
						if ( button === 'yes' ) deleteChart( chartObj.id );
					}, this);
				}
			}
		],
		fbar: [
			{
				text: 'Tallenna tiedot',
				scope: this,
				icon: '/img/icons/16x16/disk.png',
				handler: function(button) {
				
					var form = button.up('form').getForm();
					/*form.setValues({
						"chart[description]": CKEDITOR.instances.chart_description.getData()
					});*/
					form.submit({
						scope: this,
						success: function(form, action) {
							Ext.Msg.alert('Tallennus onnistui', action.result.msg);
						},
						failure: function(form, action) {
							Ext.Msg.alert('Virhe!', action.result.msg);
						}
					});
				}
			}
		]
	};
	console.log('createInfoForm');
	return infoForm;
	
}

function createChartStore ( chartObj ) {
	
	var storeFields = [];
	Ext.each(chartObj.data.fields, function(field, idx) {
		var storeField = {
			name: field.dataindex,
			type: field.type
		}
		storeFields.push(storeField);
	}, this);
	
	return Ext.create('Ext.data.ArrayStore', {
		autoDestroy: true,
		fields: storeFields,
		data: chartObj.data.data
	});	
}

function createDataGrid ( chartObj ) {
	
	var currentChartStore = createChartStore( chartObj );
	
	var gridColumns = [];
	Ext.each(chartObj.data.fields, function(field, idx) {
		var column = {
			dataIndex: field.dataindex,
			header: field.name,
			editor: {
				allowBlank: false
			}
		}
		gridColumns.push(column);
	}, this);
	
	var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
		clicksToMoveEditor: 1
	});
	var currentChartGrid = Ext.create('Ext.grid.Panel', {
		id: 'currentChartGrid',
		title: 'Taulukko',
		store: currentChartStore,
		width: 'auto',
		frame: false,
		scope: this,
		columns: gridColumns,
		selModel: {
			selType: 'cellmodel'
		},
		plugins: [rowEditing],
		tbar: [
			{
				text: 'Lis‰‰ rivi',
				scope: this,
				icon: '/img/icons/16x16/table_row_insert.png',
				handler: function( button ) {
					rowEditing.cancelEdit();
					var row = {};
					Ext.each( chartObj.data.fields, function( field ){ 
						row[ field.dataindex ] = null;
					});
					currentChartStore.add(row);
					rowEditing.startEdit(currentChartStore.getTotalCount(), 0);
				}
			},
			{
				text: 'Poista rivi...',
				scope: this,
				icon: '/img/icons/16x16/table_row_delete.png',
				handler: function( button ) {
					var sm = this.currentChartGrid.getSelectionModel();
					rowEditing.cancelEdit();
					Ext.Msg.confirm('Poista rivi', 'Haluatko varmasti poistaa valitun rivin?', function(button){
						if ( button === 'yes' ) {
							currentChartStore.removeAt(sm.getCurrentPosition().row);
							if (currentChartStore.getCount() > 0) {
								sm.select(0);
							}
						}
					}, this);
					
				}
			}
		],
		fbar: [
			{
				text: 'Tallenna taulukko',
				scope: this,
				icon: '/img/icons/16x16/disk.png',
				handler: function(button) {
					centerPanel.setLoading(true);
					var data = [];
					Ext.each(this.currentChartStore.data.items, function(record, idx) {
						var row = [];
						Ext.each( chartObj.data.fields, function( field ){ row.push(record.get(field.dataindex)) });
						data.push(row);
					}, this);
					Ext.Ajax.request({
						url: '/run/lougis/charts/updateData/',
						scope: this,
						params: {
							chart_id: chartObj.id,
							data: Ext.JSON.encode(data)
						},
						success: function( xhr ){
							var res = Ext.JSON.decode(xhr.responseText);
							if ( res.success ) {
								Ext.Msg.alert('Tallennus onnistui', res.msg);
							} else {
								Ext.Msg.alert('Virhe!', res.msg);
							}
							centerPanel.setLoading(false);
						}
					});	
				}
			}
		],
		bbar: Ext.create('Ext.PagingToolbar', {
			pageSize: 50,
			store: currentChartStore,
			displayInfo: true
		})
		
	});
	console.log('currentChartGrid');
	return currentChartGrid;
	
}

function createChartBuilder( chartObj ) {
	
	this.currentChartPreview = Ext.create('Ext.panel.Panel', {
		id: 'currentChartPreview',
		title: 'Kaavion esikatselu',
		width: 600,
		height: 400,
		items: []
	});
	
	if ( chartObj.config != null ) this.updateChartPreview( chartObj, chartObj.config );
	
	var axisFields = [];
	var xChecked = true;
	var yChecked = false;
	Ext.each(chartObj.data.fields, function(field, idx) {
	
		var aField = {
			xtype: 'fieldcontainer',
			layout: 'hbox',
			items: [
				{
					xtype: 'fieldcontainer',
					fieldLabel: field.name,
					defaultType: 'checkboxfield',
					layout: 'hbox',
					width: 300,
					defaults: {
						margin: '0 10 0 0',
						flex: 1
					},
					items: [
						{ boxLabel: 'X-akseli', name: 'chart[axes][x][fields]['+idx+']', inputValue: field.dataindex },
						{ boxLabel: 'Y-akseli', name: 'chart[axes][y][fields]['+idx+']', inputValue: field.dataindex }
					]
				},
				{
					xtype: 'displayfield',
					value: '('+this.fieldTypes[field.type]+')'
				}
			]
		}
		axisFields.push(aField);
	}, this);
	
	this.currentChartSeries = Ext.create('Ext.form.FieldContainer');
	
	this.currentChartBuilder = Ext.create('Ext.form.Panel', {
		xtype: 'form',
		id: 'chartBuilderForm',
		title: 'Kaavio',
		url: '/run/lougis/charts/saveChartConfig/',
		bodyPadding: 10,
		autoScroll: true,
		buttonAlign: 'left',
		items: [
			this.currentChartPreview,
			{
				xtype: 'hidden',
				value: chartObj.id,
				name: 'chart[id]'
			},
			{
				xtype: 'fieldset',
				title: 'Kaavion akselit',
				items: [
					axisFields,
					{
						xtype: 'textfield',
						fieldLabel: 'Y-akselin otsikko',
						name: 'chart[axes][y][title]',
						width: 500,
						value: null
					},
					{
						xtype: 'radiogroup',
						fieldLabel: 'Y-akselin tyyppi',
						columns: 2,
						width: 250,
						defaults: {
							name: 'chart[axes][y][type]'
						},
						items: [
							{ boxLabel: 'Numero', inputValue: 'Numeric', checked: true },
							{ boxLabel: 'Kategoria', inputValue: 'Category' }
						]
					},
					{
						xtype: 'textfield',
						fieldLabel: 'X-akselin otsikko',
						name: 'chart[axes][x][title]',
						width: 500,
						value: null
					},
					{
						xtype: 'radiogroup',
						fieldLabel: 'X-akselin tyyppi',
						columns: 2,
						width: 250,
						defaults: {
							name: 'chart[axes][x][type]'
						},
						items: [
							{ boxLabel: 'Numero', inputValue: 'Numeric' },
							{ boxLabel: 'Kategoria', inputValue: 'Category', checked: true }
						]
					}
				]
			},
			{
				xtype: 'fieldset',
				title: 'Kaavion legenda',
				items: [
					{
						xtype: 'fieldcontainer',
						layout: 'hbox',
						defaults: {
							margin: '0 10 0 0'	
						},
						items: [
							{
								xtype: 'radiogroup',
								fieldLabel: 'N‰kyviss‰',
								columns: 2,
								width: 220,
								defaults: {
									name: 'chart[legend][visible]'
								},
								items: [
									{ boxLabel: 'Kyll‰', inputValue: 1, checked: true },
									{ boxLabel: 'Ei', inputValue: 0 }
								]
							},
							{
								xtype: 'displayfield',
								margin: '0 0 0 15',
								value: 'Kelluvan selitteen sijainti:'
							}
						]
					},
					{
						xtype: 'fieldcontainer',
						layout: 'hbox',
						defaults: {
							margin: '0 10 0 0'	
						},
						items: [
							{
								xtype: 'combo',
								fieldLabel: 'Sijainti',
								name: 'chart[legend][position]',
								store: Ext.create('Ext.data.Store', {
									fields: ['position', 'title'],
									data: [
										{ position: 'bottom', title: 'Alhaalla' },
										{ position: 'top', title: 'Ylh‰‰ll‰' },
										{ position: 'right', title: 'Oikealla' },
										{ position: 'left', title: 'Vasemmalla' },
										{ position: 'float', title: 'Kelluva' }
									]
								}),
								queryMode: 'local',
								displayField: 'title',
								valueField: 'position',
								value: 'bottom'
							},
							{
								xtype: 'numberfield',
								fieldLabel: 'X',
								labelWidth: 20,
								width: 80,
								name: 'chart[legend][x]'
							},
							{
								xtype: 'numberfield',
								fieldLabel: 'Y',
								labelWidth: 20,
								width: 80,
								name: 'chart[legend][y]'
							}
						]
					}
				]
			},
			{
				xtype: 'fieldset',
				title: 'Kaavion kuvaajat',
				items: [
					{
						xtype: 'button',
						text: 'Lis‰‰ kuvaaja',
						margin: '10 0',
						scope: this,
						icon: '/img/icons/16x16/chart_line.png',
						handler: function( button ) {
							this.addCurrentChartSeries( chartObj );
						}
					},
					this.currentChartSeries
				]
			}
		],
		fbar: [
			
			{
				text: 'Esikatselu',
				scope: this,
				icon: '/img/icons/16x16/table_chart.png',
				handler: function(button) {
					var form = button.up('form').getForm();
					form.submit({
						scope: this,
						params: {
							save: false	
						},
						success: function(form, action) {
							this.updateChartPreview( chartObj, action.result.conf );
						},
						failure: function(form, action) {
							Ext.Msg.alert('Virhe!', action.result.msg);
						}
					});
				}
			},
			'->',
			{
				text: 'Tallenna',
				scope: this,
				icon: '/img/icons/16x16/disk.png',
				handler: function(button) {
					var form = button.up('form').getForm();
					form.submit({
						scope: this,
						params: {
							save: 'true'	
						},
						success: function(form, action) {
							if ( action.result.msg != null ) Ext.Msg.alert('Tallennus onnistui', action.result.msg);
							this.updateChartPreview( chartObj, action.result.conf );
						},
						failure: function(form, action) {
							Ext.Msg.alert('Virhe!', action.result.msg);
						}
					});
				}
			}
		]
	});
	console.log('createChartBuilder');
	return this.currentChartBuilder;
	
}

function createIframeForm( chartObj ) {
	
	
	this.iframePreview = Ext.create('Ext.form.FieldSet', {
		title: 'Esikatselu',
		items: [
			{
				xtype: 'panel',
				width: 600,
				height: 300,
				margin: '0 0 10 0',
				html: '<div style="padding: 150px 0 0 0;text-align: center"><b>Esikatselu</b></div>' 
			}
		]
	});
	this.iframeCode = Ext.create('Ext.form.field.TextArea', {
		width: 600,
		height: 200,
		value: ''
	});
	this.iframeForm = Ext.create('Ext.form.Panel', {
		xtype: 'form',
		id: 'iframeForm',
		title: 'Upotus',
		//url: '/run/lougis/charts/saveChartConfig/',
		bodyPadding: 10,
		autoScroll: true,
		buttonAlign: 'left',
		items: [
			{
				xtype: 'fieldset',
				title: 'Ikkunan asetukset',
				items: [
					{
						xtype: 'numberfield',
						name: 'width',
						width: 180,
						fieldLabel: 'Leveys',
						allowDecimals: false,
						value: 500
					},
					{
						xtype: 'numberfield',
						name: 'height',
						width: 180,
						fieldLabel: 'Korkeus',
						allowDecimals: false,
						value: 300
					},
					{
						xtype: 'button',
						text: 'Luo koodi',
						margin: '10 0',
						scope: this,
						icon: '/img/icons/16x16/table_chart.png',
						handler: function( button ) {
							this.updateIframe( chartObj );
						}
					}
				]
			},
			this.iframePreview,
			{
				xtype: 'fieldset',
				title: 'Upotuskoodi',
				items: [ this.iframeCode ]
			}
		]
	});
	console.log('createIframeForm');
	return this.iframeForm;
	
}
    
function updateIframe( chartObj ) {

	this.iframePreview.setLoading(true);
	var vals = this.iframeForm.getForm().getValues();
	
	Ext.Ajax.request({
		url: '/run/lougis/charts/buildIframeCode/',
		scope: this,
		params: {
			chart_id: chartObj.id,
			width: vals.width,
			height: vals.height
		},
		success: function( xhr ){
			var res = Ext.JSON.decode(xhr.responseText);
			if ( res.success ) {
				this.iframePreview.removeAll();
				var panel = {
					xtype: 'panel',
					width: parseInt(vals.width),
					height: parseInt(vals.height),
					padding: 0,
					margin: '0 0 10 0',
					html: res.code 
				};
				
				this.iframePreview.add(panel);
				
				this.iframeCode.setValue(res.code);
			} else {
				Ext.Msg.alert('Virhe!', res.msg);
			}
			this.iframePreview.setLoading(false);
		}
	});	
	console.log('updateIframe');
}
    
function createChartBuilder( chartObj ) {
	
	var currentChartPreview = Ext.create('Ext.panel.Panel', {
		id: 'currentChartPreview',
		title: 'Kaavion esikatselu',
		width: 600,
		height: 400,
		items: []
	});
	
	if ( chartObj.config != null ) updateChartPreview( chartObj, chartObj.config );
	
	var axisFields = [];
	var xChecked = true;
	var yChecked = false;
	Ext.each(chartObj.data.fields, function(field, idx) {
	
		var aField = {
			xtype: 'fieldcontainer',
			layout: 'hbox',
			items: [
				{
					xtype: 'fieldcontainer',
					fieldLabel: field.name,
					defaultType: 'checkboxfield',
					layout: 'hbox',
					width: 300,
					defaults: {
						margin: '0 10 0 0',
						flex: 1
					},
					items: [
						{ boxLabel: 'X-akseli', name: 'chart[axes][x][fields]['+idx+']', inputValue: field.dataindex },
						{ boxLabel: 'Y-akseli', name: 'chart[axes][y][fields]['+idx+']', inputValue: field.dataindex }
					]
				},
				{
					xtype: 'displayfield',
					value: '('+this.fieldTypes[field.type]+')'
				}
			]
		}
		axisFields.push(aField);
	}, this);
	
	var currentChartSeries = Ext.create('Ext.form.FieldContainer');
	
	var currentChartBuilder = Ext.create('Ext.form.Panel', {
		xtype: 'form',
		id: 'chartBuilderForm',
		title: 'Kaavio',
		url: '/run/lougis/charts/saveChartConfig/',
		bodyPadding: 10,
		autoScroll: true,
		buttonAlign: 'left',
		items: [
			currentChartPreview,
			{
				xtype: 'hidden',
				value: chartObj.id,
				name: 'chart[id]'
			},
			{
				xtype: 'fieldset',
				title: 'Kaavion akselit',
				items: [
					axisFields,
					{
						xtype: 'textfield',
						fieldLabel: 'Y-akselin otsikko',
						name: 'chart[axes][y][title]',
						width: 500,
						value: null
					},
					{
						xtype: 'radiogroup',
						fieldLabel: 'Y-akselin tyyppi',
						columns: 2,
						width: 250,
						defaults: {
							name: 'chart[axes][y][type]'
						},
						items: [
							{ boxLabel: 'Numero', inputValue: 'Numeric', checked: true },
							{ boxLabel: 'Kategoria', inputValue: 'Category' }
						]
					},
					{
						xtype: 'textfield',
						fieldLabel: 'X-akselin otsikko',
						name: 'chart[axes][x][title]',
						width: 500,
						value: null
					},
					{
						xtype: 'radiogroup',
						fieldLabel: 'X-akselin tyyppi',
						columns: 2,
						width: 250,
						defaults: {
							name: 'chart[axes][x][type]'
						},
						items: [
							{ boxLabel: 'Numero', inputValue: 'Numeric' },
							{ boxLabel: 'Kategoria', inputValue: 'Category', checked: true }
						]
					}
				]
			},
			{
				xtype: 'fieldset',
				title: 'Kaavion legenda',
				items: [
					{
						xtype: 'fieldcontainer',
						layout: 'hbox',
						defaults: {
							margin: '0 10 0 0'	
						},
						items: [
							{
								xtype: 'radiogroup',
								fieldLabel: 'N‰kyviss‰',
								columns: 2,
								width: 220,
								defaults: {
									name: 'chart[legend][visible]'
								},
								items: [
									{ boxLabel: 'Kyll‰', inputValue: 1, checked: true },
									{ boxLabel: 'Ei', inputValue: 0 }
								]
							},
							{
								xtype: 'displayfield',
								margin: '0 0 0 15',
								value: 'Kelluvan selitteen sijainti:'
							}
						]
					},
					{
						xtype: 'fieldcontainer',
						layout: 'hbox',
						defaults: {
							margin: '0 10 0 0'	
						},
						items: [
							{
								xtype: 'combo',
								fieldLabel: 'Sijainti',
								name: 'chart[legend][position]',
								store: Ext.create('Ext.data.Store', {
									fields: ['position', 'title'],
									data: [
										{ position: 'bottom', title: 'Alhaalla' },
										{ position: 'top', title: 'Ylh‰‰ll‰' },
										{ position: 'right', title: 'Oikealla' },
										{ position: 'left', title: 'Vasemmalla' },
										{ position: 'float', title: 'Kelluva' }
									]
								}),
								queryMode: 'local',
								displayField: 'title',
								valueField: 'position',
								value: 'bottom'
							},
							{
								xtype: 'numberfield',
								fieldLabel: 'X',
								labelWidth: 20,
								width: 80,
								name: 'chart[legend][x]'
							},
							{
								xtype: 'numberfield',
								fieldLabel: 'Y',
								labelWidth: 20,
								width: 80,
								name: 'chart[legend][y]'
							}
						]
					}
				]
			},
			{
				xtype: 'fieldset',
				title: 'Kaavion kuvaajat',
				items: [
					{
						xtype: 'button',
						text: 'Lis‰‰ kuvaaja',
						margin: '10 0',
						scope: this,
						icon: '/img/icons/16x16/chart_line.png',
						handler: function( button ) {
							addCurrentChartSeries( chartObj );
						}
					},
					currentChartSeries
				]
			}
		],
		fbar: [
			
			{
				text: 'Esikatselu',
				scope: this,
				icon: '/img/icons/16x16/table_chart.png',
				handler: function(button) {
					var form = button.up('form').getForm();
					form.submit({
						scope: this,
						params: {
							save: false	
						},
						success: function(form, action) {
							updateChartPreview( chartObj, action.result.conf );
						},
						failure: function(form, action) {
							Ext.Msg.alert('Virhe!', action.result.msg);
						}
					});
				}
			},
			'->',
			{
				text: 'Tallenna',
				scope: this,
				icon: '/img/icons/16x16/disk.png',
				handler: function(button) {
					var form = button.up('form').getForm();
					form.submit({
						scope: this,
						params: {
							save: 'true'	
						},
						success: function(form, action) {
							if ( action.result.msg != null ) Ext.Msg.alert('Tallennus onnistui', action.result.msg);
							updateChartPreview( chartObj, action.result.conf );
						},
						failure: function(form, action) {
							Ext.Msg.alert('Virhe!', action.result.msg);
						}
					});
				}
			}
		]
	});
	console.log('createChartBuilder');
	return currentChartBuilder;
	
}

function deleteChart ( chartId) {

	this.centerPanel.setLoading(true);

	Ext.Ajax.request({
		url: '/run/lougis/charts/deleteChart/',
		scope: this,
		params: {
			chart_id: chartId
		},
		success: function( xhr ){
			var res = Ext.JSON.decode(xhr.responseText);
			if ( res.success ) {
				Ext.Msg.alert('Kierros poistettu', res.msg);
				this.reloadChartTreeStore();
				this.centerPanel.removeAll(true);
				this.centerPanel.add( this.createChartStartPanel() );
			} else {
				Ext.Msg.alert('Virhe!', res.msg);
			}
			this.centerPanel.setLoading(false);
		}
	});		
	

}