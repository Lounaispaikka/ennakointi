/**
 * Returns Ext.data.store records as an array.
 * @method
 * @member Ext
 * @author Ville Glad
 */
Ext.getStoreDataAsArray = function( store ) {
	var data = [];
	Ext.each(store.getRange(), function(record) {
		data.push(record.data);
	});
	return data;
};

Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.util.*',
    'Ext.state.*',
    'Ext.form.*',
    'Ext.ux.CheckColumn',
    'Ext.selection.CheckboxModel'
]);
Ext.define('Lougis.view.Tietopankki', {
    extend: 'Lougis.view.Panel',
    alias: 'widget.tietopankki',
    id: 'LougisTietopankki',
	anchor: '100% 100%',
	border: 0,
    items: [],
    treeData: [],
    initComponent: function() {
    
		this.callParent();
		
		this.ennakointiStartPanel = this.createEnnakointiStartPanel();
		
		this.centerPanel = Ext.create('Ext.panel.Panel', {
			layout: 'fit',
			border: 0,
			anchor: '100% 100%',
			items: [ this.ennakointiStartPanel
			]
		});
		this.add(this.centerPanel);
        
    }
	,createEnnakointiStartPanel: function() {
		this.btnUusiAihe = Ext.create('Ext.Button', {
			text: 'Uusi teema',
			scale: 'large',
			scope:this,
			handler: function() {
				alert('You clicked Uusi teema!')
			}			
		});
		this.btnLisaaTavara = Ext.create('Ext.Button', {
			text: 'Lis&auml;&auml; tavaraa',
			scale: 'large',
			scope:this,
			handler: function() {
				alert('You clicked Lis&auml;&auml; tavaraa!')
			}	
		});
		this.ennakointiPanel = Ext.create('Ext.panel.Panel', {
			title: 'Koulutuksen ennakointi - sis&auml;ll&ouml;n luominen',
			items: [this.btnUusiAihe, this.btnLisaaTavara]
		});
		console.log(this.ennakointiPanel);
		return this.ennakointiPanel;
	}
	
            
});