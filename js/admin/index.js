intelli.index = function()
{
	var vUrl = intelli.config.admin_url+'/home/lyrics/read.json';

	return {
		lyricsChart: null,
		statisticsPanel: null,
		portal: null,
		vUrl: vUrl,

		chartStore: new Ext.data.JsonStore({
			fields: ['statuses', 'total'],
			url: vUrl + '?action=getlyricschart&_debug=false'
		}),
		statusesStoreFilter: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [
				['all', _t('_status_')],
				['active', _t('active')],
				['approval', _t('approval')]
			]
		}),
		pagingStore: new Ext.data.SimpleStore(
		{
			fields: ['value', 'display'],
			data : [['10', '10'],['20', '20'],['30', '30'],['40', '40'],['50', '50']]
		})
	};
}();

Ext.onReady(function()
{
	intelli.index.chartStore.load();

	intelli.index.lyricsChart = new Ext.Panel(
	{
        items:
		{
            store: intelli.index.chartStore,
            xtype: 'piechart',
			height: 200,
            dataField: 'total',
            categoryField: 'statuses',
            //extra styles get applied to the chart defaults
            extraStyle:
            {
                legend:
                {
                    display: 'bottom',
                    padding: 5,
                    font:
                    {
                        family: 'Tahoma',
                        size: 13
                    }
                }
            }
        }
    });

	/*
	 * Statistics panel
	 */
	intelli.index.statisticsPanel = new Ext.Panel(
	{
		el: 'box_statistics',
		listeners:
		{
			'afterrender': function(cmp)
			{
				cmp.el.setStyle("display", "block");
			}
		}
	});
	
	var leftSide = [];
	var rightSide = [];
	var modules = [
	{
		id: 'statistics',
		column:'left',
		pos: 0,
		cfg:{
			title: _t('statistics'),
			id: 'statistics',
			style: 'margin: 5px 0 5px 0',
			items: intelli.index.statisticsPanel
		}
	},{
		id: 'lyrics_chart',
		column:'right',
		pos: 0,
		cfg:{
			title: _t('lyrics'),
			id: 'lyrics_chart',
			style: 'margin: 5px 0 5px 0',
			items: intelli.index.lyricsChart
		}
	}];

	var left_pos = [];
	var right_pos = [];
	jQuery.each(modules, function(i, item) { 
		
		var cfg = [item.column,item.pos];
		if (Ext.util.Cookies.get(item.id)) {
			cfg = Ext.util.Cookies.get(item.id).split('-');
		}
		else {
			Ext.util.Cookies.set(item.id, item.column + '-' + item.pos);
		} 
		
		if(cfg[0] == 'left' || cfg[0] == 0)
		{
			if(!left_pos[cfg[1]]) left_pos[cfg[1]] = (cfg[1] + 1) * 10;
			leftSide[left_pos[cfg[1]]] = item.cfg;
			
			left_pos[cfg[1]]--;
		}
		else if(cfg[0] == 'right' || cfg[0] == 1)
		{
			if(!right_pos[cfg[1]]) right_pos[cfg[1]] = (cfg[1] + 1) * 10;
			rightSide[right_pos[cfg[1]]] = item.cfg;
			
			right_pos[cfg[1]]--;
		}
	});
	leftSide = jQuery.map(leftSide, function(n, i){return (!n ? null : n);});
	rightSide = jQuery.map(rightSide, function(n, i){return (!n ? null : n);});
	
	intelli.index.portal = new Ext.Panel(
	{
		renderTo: 'box_panels_content',
		border: false,
		stateful: true,
		autoHeight: true,
		items:[
		{
            xtype:'portal',
			border: false,
			stateful: true,
			id: 'ia_portal',
            margins: '35 5 5 0',
			//stateful:true,
			getState: function()
			{
			},

			applyState: function(state, config)
			{
			},
			stateEvents: ['drop'],
            items:[
			{
                columnWidth:.50,
                items: leftSide
            },{
                columnWidth:.50,
                style: 'padding: 0 0 0 10px',
                items: rightSide
            }]
			,listeners: {
	            'drop': function(e){
					Ext.util.Cookies.set(e.panel.id, e.columnIndex + '-' + e.position);
	            }
	        }
        }]

	});
});