Ext.define('Lougis.view.Comments', {

    extend: 'Lougis.view.Panel',
    alias: 'widget.comments',
    id: 'LougisComments',
	anchor: '100% 100%',
	border: 0,
    items: [],
    
    initComponent: function() {
    
		this.callParent();
		
		this.commentsGrid = this.createCommentsGrid();
		
		this.add(this.commentsGrid);
        
    }
    
    ,createCommentsGrid: function() {
	    
	    
	    
    }
            
});