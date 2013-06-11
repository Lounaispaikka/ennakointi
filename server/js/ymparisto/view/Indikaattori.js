/**
 * Returns Ext.data.store records as an array.
 * @method
 * @member Ext
 * @author Pyry Liukas
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
        'Ext.chart.*',
        'Ext.ux.CheckColumn'
]);
//Ext.define(className, members, onClassCreated);
Ext.define('Ymparisto.view.Indikaattori', {
        extend: 'Lougis.view.Panel',
        alias: 'widget.charts',
        id: 'YmparistoIndikaattori',
        anchor: '100% 100%',
        border: 0,
        items: [],
        treeData: [],
        
        initComponent: function() {
                this.callParent();
                this.chartTreePanel = this.createChartTree();
                this.TilastoPanel = Ext.create('Ext.panel.Panel', {
			layout: 'border',
			anchor: '100% 100%',
                        renderTo: 'chartdiv',
			border: 0                
		});
        },
        
        createChartTree: function() {
                Ext.define('ChartList', {
                        extend: 'Ext.data.Model',
                        id: 'chartList',
                        fields: [
                                {name: 'chart_id', type: 'int'},
                                {name: 'text', type: 'string'}
                        ]
                });
                var chartListStore = Ext.create('Ext.data.Store', {
                        model: 'ChartList',
                        id: 'chartListStore',
                        fields: [ 'chart_id', 'text' ],
                        proxy: {
                                type: 'ajax',
                                //url: '/run/lougis/charts/getPublishedChartsJson/',
                                url: '/run/lougis/charts/getChartsJson/',
                                reader: {
                                        type: 'json'
                                }
                        },
                        folderSort: false,
                        root: null,
                        autoLoad:true
                });

                this.gridPanel = Ext.create('Ext.grid.Panel', {
                        id: 'ChartGridPanel',
                        renderTo: 'leftCol',
                        autoScroll: false,
                        border:false,
                        store: 'chartListStore',
                        width: 204,
                        scroll: 'none',
                        cls: 'ind-grid',
                        columns: [
                                //{ dataIndex: 'chart_id', width: 50},
                                { dataIndex: 'text', width: 204}
                        ],
                        hideHeaders: true,
                        layout: 'fit',

                        listeners: {
                                itemclick : function(gridPanel, record, item, index, e) {
                                        var id = record.get('chart_id');
                                        getTilastoInfo(id);
                                        console.log(id);
                                        //tilastoPanel.clear();
                                        //tilastoPanel.doLayout();
                                        //console.log(tilastoPanel);
                                }
                        }
                });
                return this.gridPanel;
        },
        
        getTilastoInfo: function(chartId) {
                this.are = Ext.Ajax.request({
                        url: '/run/lougis/charts/getChartInfo/',
                        params: {
                                chart_id: chartId
                        },
                        success: function( xhr ){
                                res = Ext.JSON.decode(xhr.responseText);

                                createTilastoGrid(res.chart);
                        }
                });
                return this.are;
        },
        createTilastoGrid: function(chartInfo) {
                //1. Luodaan tilaston tietokantadatasta perustiedot
        /**********************************************************************/  
                Ext.define('TilastoTiedot', {
                        extend: 'Ext.data.Model',
                        fields: ['created_by', 'description', 'id', 'original_filename', 'published', 'short_description', 'title', 'updated_date'] 
                });

                var store = Ext.create('Ext.data.Store', {
                        autoLoad: true,
                        model: 'TilastoTiedot',
                        data : chartInfo,
                        proxy: {
                                type: 'memory',
                                reader: {
                                        type: 'json'
                                }
                        }
                });



                var infoForm = Ext.create('Ext.grid.Panel', {  
                        title: 'Perustiedot',
                        id: 'chartInfoForm',
                        columns: [
                                { header: 'id', dataIndex: 'id', width: 50},
                                { header: 'original_filename', dataIndex: 'original_filename', width: 199},
                                { header: 'description', dataIndex: 'description', width: 199},
                                { header: 'published', dataIndex: 'published', width: 10},
                                { header: 'short_description', dataIndex: 'short_description', width: 100},
                                { header: 'title', dataIndex: 'title', width: 100},
                                { header: 'updated_date', dataIndex: 'updated_date', width: 50}
                        ],
                       // items: [{id: chartInfo.id}],
                        store: store,
                        renderTo: 'chartdiv',
                        width: 600
                });

                //var series1 = chartInfo.config.series;

                //2. Luodaan chart ja panel tilastoa varten
        /**********************************************************************/
                var storeDataFields = [];
                        Ext.each(chartInfo.data.fields, function(field, idx) {
                                var storeField = {
                                        name: field.dataindex,
                                        type: field.type
                                }
                                storeDataFields.push(storeField);
                        }, this);

                var dataStore = Ext.create('Ext.data.ArrayStore', {
                        autoDestroy: true,
                        fields: storeDataFields,            
                        data: chartInfo.data.data
                });

                this.tilastoChart = Ext.create('Ext.chart.Chart', {
                       //series: series1
                       store: dataStore,
                       axes: chartInfo.config.axes,
                       series: chartInfo.config.series,
                       xtype:'chart',
                       shadow:true,
                       //animate:true,
                       config:chartInfo.config,
                       width: 550,
                       height: 350
                });
                console.log(tilastoChart);
                this.tilastoPanel = Ext.create('Ext.panel.Panel', {
                        renderTo: 'chartdiv',
                        width: 550,
                        height: 350           
                     });
                tilastoPanel.add(tilastoChart);
                tilastoPanel.doLayout();
                
        /**********************************************************************/    
                return this.tilastoPanel;
        }
});


