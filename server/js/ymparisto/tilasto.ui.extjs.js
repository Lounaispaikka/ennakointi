//Legendan override funktio. Rivitt‰‰ selitteet.
Ext.override(Ext.chart.Legend, {
createItems: function() {
var me = this,
chart = me.chart,
surface = chart.surface,
items = me.items,
padding = me.padding,
itemSpacing = me.itemSpacing,
spacingOffset = 2,
itemWidth = 0,
itemHeight = 0,
totalWidth = 0,
totalHeight = 0,
vertical = me.isVertical,
math = Math,
mfloor = math.floor,
mmax = math.max,
index = 0,
i = 0,
len = items ? items.length : 0,
x, y, item, bbox, height, width,
// chart dimensions
chartBBox = chart.chartBBox,
chartInsets = chart.insetPadding,
chartWidth = chartBBox.width - (chartInsets * 2),
chartHeight = chartBBox.height - (chartInsets * 2),
xOffset = 0, yOffset = 0,
legendWidth = 0, legendHeight = 0,
legendXOffset = 50, legendYOffset = 50,
hSpacing, vSpacing;

//remove all legend items
if (len) {
for (; i < len; i++) {
items[i].destroy();
}
}
//empty array
items.length = [];

// Create all the item labels, collecting their dimensions and positioning each one
// properly in relation to the previous item
chart.series.each(function(series, i) {
if (series.showInLegend) {
Ext.each([].concat(series.yField), function(field, j) {
item = new Ext.chart.LegendItem({
legend: this,
series: series,
surface: chart.surface,
yFieldIndex: j
});
bbox = item.getBBox();

width = bbox.width;
height = bbox.height;

itemWidth = mmax(itemWidth, width);
itemHeight = mmax(itemHeight, height);

items.push(item);
}, this);

}
}, me);

//spacing = itemSpacing / (vertical ? 2 : 1);
vSpacing = itemSpacing / 2;
hSpacing = itemSpacing;
if (vertical) {
if ( chartHeight - legendYOffset < items.length * (itemHeight + vSpacing) + 2 * padding + vSpacing) {
legendHeight = chartHeight - legendYOffset;
yOffset = mfloor((legendHeight - mfloor((legendHeight - 2 * padding - vSpacing) / (itemHeight + vSpacing)) * (itemHeight + vSpacing) ) / 2);
}
else {
legendHeight = items.length * (itemHeight + vSpacing) + 2 * padding + vSpacing;
yOffset = vSpacing + padding;
}
xOffset = hSpacing + padding;
totalWidth = xOffset;
totalHeight = yOffset;
}
else {
if ( chartWidth - legendXOffset < items.length * (itemWidth + hSpacing) + 2 * padding + hSpacing) {
legendWidth = chartWidth - legendXOffset;
xOffset = mfloor((legendWidth - mfloor((legendWidth - 2 * padding - hSpacing) / (itemWidth + hSpacing)) * (itemWidth + hSpacing) ) / 2);
}
else {
legendWidth = items.length * (itemWidth + hSpacing) + 2 * padding + hSpacing;
xOffset = padding + hSpacing;
}
yOffset = padding + vSpacing;
totalHeight = yOffset;
totalWidth = xOffset;
}

Ext.each(items, function(item, j) {
if (vertical && (totalHeight + vSpacing + itemHeight > chartHeight - legendYOffset)) {
totalHeight = yOffset;
totalWidth += itemWidth + hSpacing;
}
else if (!vertical && (totalWidth + hSpacing + itemWidth > chartWidth - legendXOffset)) {
totalWidth = xOffset;
totalHeight += itemHeight + vSpacing;
}
item.x = totalWidth;
item.y = mfloor(totalHeight + itemHeight / 2);

// Collect cumulative dimensions
if (vertical)
totalHeight += itemHeight + vSpacing;
else
totalWidth += itemWidth + hSpacing;

}, me);

// Store the collected dimensions for later
me.width = mfloor(vertical ? totalWidth + itemWidth + xOffset : legendWidth);
me.height = mfloor(vertical ? legendHeight : totalHeight + itemHeight + yOffset );
me.itemHeight = itemHeight;
me.itemWidth = itemWidth;
}

});
/**********************************************************************/
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
    'Ext.view.View',
]);
console.log("11");
function createPanels() {
        var gridPanel = createChartTree();
        var tilastoPanel = Ext.create('Ext.panel.Panel', {
			layout: 'fit',
			anchor: '100% 100%',
                        renderTo: 'content',
			border: 0                
		});
       console.log("1");      
function createChartTree() {
   
        var treeStore = Ext.create('Ext.data.TreeStore', {
               id:'treeStore',
               fields: [ 'chart_id', 'text' ],
               proxy: {
                        type: 'ajax',
                        url: '/run/lougis/charts/getPublishedChartsJson/',
                       // url: '/run/lougis/charts/getChartsJson/',
                        reader: {
                                type: 'json'
                        }
                },
                folderSort: false,
                root: null,
                autoLoad: true
        });
        
        this.treePanel = Ext.create('Ext.tree.Panel', {

                cls: 'ind-tree',
                height: 1500,
                width: 219,
                store: treeStore,
                border:false,
                rootVisible: false,
                
                renderTo: 'leftCol',
                
                listeners: {
                        itemclick : function(gridPanel, record, item, index, e) {
                                var id = record.get('chart_id');
                                getTilastoInfo(id);
                                $('#cms_data').hide(); //piilottaa indikaattorisivulta sis‰llˆnhallinasta tulevan tiedon
                                tilastoPanel.setLoading(true);
                        } ,
                        load: function(){
                                resetHeight(this);
                            },
                            itemexpand: function(){
                                resetHeight(this);
                            },
                            itemcollapse: function(){
                                resetHeight(this);
                            }

                        }      
        });
        function resetHeight(cmp){
                setTimeout(function(){
                    var innerElement = cmp.getEl().down('table.x-grid-table');
                    if(innerElement){
                        var height = innerElement.getHeight();
                        height+=20;
                        cmp.setHeight(height);
                    }
                }, 200);
       }

}

function getTilastoInfo(chartId) {
        var are = Ext.Ajax.request({
                url: '/run/lougis/charts/getChartInfo/',
                params: {
                        chart_id: chartId
                },
                success: function( xhr ){
                        res = Ext.JSON.decode(xhr.responseText);
                      
                        createTilastoGrid(res.chart);
                }         
	});
        
}

function createTilastoGrid(chartInfo) {
        //1. Luodaan tilaston tietokantadatasta perustiedot
/**********************************************************************/  
       
        chartInfo.updated_date = chartInfo.updated_date.substr(0,11);
        
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
        //Otsikko
        var tpl_h = new Ext.XTemplate(
                '<tpl for=".">',
                        '<div style="margin-bottom: 10px;" class="chartinfo">',
                          '<h2>{title}</h2>',
                          '<p><span  class="pvm">Tilasto p√§ivitetty: {updated_date}</span></p>',
                        '</div>',
                '</tpl>'
        );
        
               
        var infoHeading = Ext.create('Ext.view.View', {
                store: store,
                itemSelector: 'div.chartinfo',
                tpl: tpl_h,
                id: 'chartInfoHeading',
                width: 680
        });
        //Kuvaukset
        var tpl_d = new Ext.XTemplate(
                '<tpl for=".">',
                        '<div style="margin-bottom: 10px;" class="chartinfo">',
                        '<p><span class="short_desc">{short_description}</span></p>',
                        '<p>{description}</p>',
                        '</div>',
                '</tpl>'
        );
        
        var infoDescription = Ext.create('Ext.view.View', {
                store: store,
                itemSelector: 'div.chartinfo',
                tpl: tpl_d,
                id: 'chartInfoDescription',
                width: 680
        });
     
        //2. Luodaan chart ja panel tilastoa varten
/**********************************************************************/
        var storeDataFields = [];
                Ext.each(chartInfo.data.fields, function(field, idx) {
                        var storeField = {
                                name: field.dataindex,
                                type: field.type
                        };
                        storeDataFields.push(storeField);
                }, this);

        var dataStore = Ext.create('Ext.data.ArrayStore', {
                autoDestroy: true,
                fields: storeDataFields,            
                data: chartInfo.data.data
        });
        var colors = ["#94ae0a", "#115fa6","#a61120", /*"#ff8809"*/"#595959", "#ffd13e", "#a61187", "#24ad9a", "#7c7474", "#a66111"];
        Ext.define('Ext.chart.theme.Indit', {
            extend: 'Ext.chart.theme.Base',
            
            constructor: function(config) {
                this.callParent([Ext.apply({
                    axisTitleLeft: {
                            font: 'bold 12px Verdana'
                    },
                    axisTitleBottom: {
                            font: 'bold 12px Verdana'
                    },
                    colors: colors
                   // colors: colors
                }, config)]);
            }
        });
        
        var serie = chartInfo.config.series;
       
        function rend(storeItem, item) {
                this.setTitle(item.value[1]);
        }
  
        $.each(serie, function(index) {
                serie[index].tips = new Array();
                serie[index].tips.trackMouse = true;
                serie[index].tips.width = 65;
                serie[index].tips.height = 28;
                serie[index].tips.renderer = rend;   
                
        });
        var axes = chartInfo.config.axes;
        
      
        carr = chartInfo.data.data;
         
        
         // Lis‰‰ minimum-arvon 0, jos axes type on numeric (== kuvaaja pakotetaan alkaa nollasta)
         var neg = false;
         $.each(carr, function(index) {
            
                 $.each(carr[index], function(i) {
               
                        if(carr[index][i] < 0) {
                                neg = true;
                        }
                        
                });
         });
         if(neg === false) {
                $.each(axes, function(index) {
                        if(axes[index].type === 'Numeric') { 
                                axes[index].minimum = 0;
                        } 
                });
         }

        var tilastoChart = Ext.create('Ext.chart.Chart', {
               store: dataStore,
               theme: 'Indit',
               axes: axes,
              
               series: serie,
               legend: chartInfo.config.legend,
               xtype:'chart',
               shadow: true,
              
               config:chartInfo.config,
               width: 600,
               height: 400,
               border: 0
        });
       tilastoPanel.destroy();
       tilastoPanel.doLayout();
      
       tilastoPanel = Ext.create('Ext.panel.Panel', {
                renderTo: 'chartdiv',
                width: 680,
                border: 0
             });
       
        tilastoPanel.add(infoHeading);
        tilastoPanel.add(tilastoChart);
        tilastoPanel.add(infoDescription);
        
        tilastoPanel.doLayout();
       
/**********************************************************************/    
tilastoPanel.setLoading(false);
}
}
