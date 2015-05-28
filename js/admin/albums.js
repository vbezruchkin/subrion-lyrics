intelli.album = function()
{	
	return {
		oGrid: null,
		title: _t('manage_albums'),
		url: intelli.config.admin_url + '/manage/albums/',
		removeBtn: true,
		progressBar: true,
		texts: {
			confirm_one: _t('are_you_sure_to_delete_selected_album'),			
			confirm_many: _t('are_you_sure_to_delete_selected_albums')
		},
		statusesStore: ['active', 'inactive'],
		record:['title', 'title_alias', 'description', 'artist_title', 'date_added', 'year', 'date_modified', 'status', 'edit', 'remove'],
		columns:[
			'checkcolumn',
			{
				custom: 'expander',
				tpl: '{description}'
			},{
				header: _t('title'), 
				dataIndex: 'title', 
				sortable: true, 
				width: 200
			},{
				header: _t('title_alias'), 
				dataIndex: 'title_alias', 
				sortable: true,
				hidden: true,
				width: 170
			},{
				header: _t('artist'), 
				dataIndex: 'artist_title',
				sortable: true, 
				width: 200
			},{
				header: _t('year'), 
				dataIndex: 'year',
				sortable: false,
				hidden: true,
				width: 50,
				editor: new Ext.form.TextField({
					allowBlank: true
				})
			},{
				header: _t('date_added'), 
				dataIndex: 'date_added',
				width: 130,
				sortable: true
			},{
				header: _t('date_modified'), 
				dataIndex: 'date_modified',
				width: 130,
				sortable: true
			},
			'status',{
				custom: 'edit',
				redirect: intelli.config.admin_url+'/manage/albums/edit/?id=',
				icon: 'edit-grid-ico.png',
				title: _t('edit')
			}
			,'remove'
		]
	};
}();

Ext.onReady(function(){
	intelli.album.oGrid = new intelli.exGrid(intelli.album);
	
	intelli.album.oGrid.cfg.tbar = new Ext.Toolbar(
	{
		items:[
		_t('title') + ':',
		{
			xtype: 'textfield',
			name: 'searchTitle',
			id: 'searchTitle',
			emptyText: 'Enter title'
		},
		_t('artist') + ':',
		{
			xtype: 'textfield',
			name: 'searchArtist',
			id: 'searchArtist',
			emptyText: 'Enter artist'
		},
		_t('status') + ':',
		{
			xtype: 'combo',
			typeAhead: true,
			triggerAction: 'all',
			editable: false,
			lazyRender: true,
			store: intelli.album.oGrid.cfg.statusesStoreWithAll,
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
				var artist = Ext.getCmp('searchArtist').getValue();
				var status = Ext.getCmp('stsFilter').getValue();

				if('' != title || '' != artist || '' != status)
				{
					intelli.album.oGrid.dataStore.baseParams =
					{
						action: 'get',
						status: status,
						artist: artist,
						title: title
					};

					intelli.album.oGrid.dataStore.reload();
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
				Ext.getCmp('searchArtist').reset();
				Ext.getCmp('stsFilter').setValue('all');

				intelli.album.oGrid.dataStore.baseParams =
				{
					action: 'get',
					title: '',
					artist: '',
					status: ''
				};

				intelli.album.oGrid.dataStore.reload();
			}
		}]
	});
	intelli.album.oGrid.init();
	
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