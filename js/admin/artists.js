intelli.artist = function()
{	
	return {
		oGrid: null,
		title: _t('manage_artists'),
		url: intelli.config.admin_url + '/manage/artists/',
		removeBtn: true,
		progressBar: true,
		texts: {
			confirm_one: _t('are_you_sure_to_delete_selected_artist'),			
			confirm_many: _t('are_you_sure_to_delete_selected_artists')
		},
		statusesStore: ['active', 'inactive'],
		record:['title', 'title_alias', 'description', 'date_added', 'date_modified', 'status', 'edit', 'remove'],
		columns:[
			'checkcolumn',
			{
				custom: 'expander',
				tpl: '{description}'
			},{
				header: _t('title'), 
				dataIndex: 'title', 
				sortable: true, 
				width: 250
			},
			{
				header: _t('title_alias'), 
				dataIndex: 'title_alias', 
				sortable: true, 
				width: 250
			},
			{
				header: _t('date_added'), 
				dataIndex: 'date_added',
				width: 130,
				sortable: true
			},
			{
				header: _t('date_modified'), 
				dataIndex: 'date_modified',
				width: 130,
				sortable: true
			},
			'status',{
				custom: 'edit',
				redirect: intelli.config.admin_url+'/manage/artists/edit/?id=',
				icon: 'edit-grid-ico.png',
				title: _t('edit')
			}
			,'remove'
		]
	};
}();

Ext.onReady(function(){
	intelli.artist.oGrid = new intelli.exGrid(intelli.artist);

	intelli.artist.oGrid.cfg.tbar = new Ext.Toolbar(
	{
		items:[
		_t('title') + ':',
		{
			xtype: 'textfield',
			name: 'searchTitle',
			id: 'searchTitle',
			emptyText: 'Enter title'
		},
		_t('status') + ':',
		{
			xtype: 'combo',
			typeAhead: true,
			triggerAction: 'all',
			editable: false,
			lazyRender: true,
			store: intelli.artist.oGrid.cfg.statusesStoreWithAll,
			value: 'all',
			displayField: 'display',
			valueField: 'value',
			mode: 'local',
			id: 'stsFilter'
		},{
			text: _t('search'),
			iconCls: 'search-grid-ico',
			id: 'fltBtn',
			handler: function()
			{
				var title = Ext.getCmp('searchTitle').getValue();
				var status = Ext.getCmp('stsFilter').getValue();

				if('' != title || '' != status)
				{
					intelli.artist.oGrid.dataStore.baseParams =
					{
						action: 'get',
						status: status,
						title: title
					};

					intelli.artist.oGrid.dataStore.reload();
				}
			}
		},
		'-',
		{
			text: _t('reset'),
			id: 'resetBtn',
			handler: function()
			{
				Ext.getCmp('searchTitle').reset();
				Ext.getCmp('stsFilter').setValue('all');

				intelli.artist.oGrid.dataStore.baseParams =
				{
					action: 'get',
					title: '',
					status: ''
				};

				intelli.artist.oGrid.dataStore.reload();
			}
		}]
	});
	intelli.artist.oGrid.init();
	
	if(intelli.urlVal('status'))
	{
		Ext.getCmp('stsFilter').setValue(intelli.urlVal('status'));
	}

	var search = intelli.urlVal('quick_search');
	
	if(null != search)
	{
		Ext.getCmp('searchTitle').setValue(search);
	}
});