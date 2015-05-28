intelli.lyric = function()
{	
	return {
		oGrid: null,
		title: _t('manage_lyrics'),
		url: intelli.config.admin_url + '/manage/lyrics/',
		removeBtn: true,
		progressBar: true,
		texts: {
			confirm_one: _t('are_you_sure_to_delete_selected_lyric'),			
			confirm_many: _t('are_you_sure_to_delete_selected_lyrics')
		},
		statusesStore: ['active', 'inactive'],
		record:['title', 'title_alias', 'artist', 'account', 'body', 'date_added', 'date_modified', 'status', 'edit', 'remove'],
		columns:[
			'checkcolumn',
			{
				header: _t('title'), 
				dataIndex: 'title', 
				sortable: true, 
				width: 250,
				editor: new Ext.form.TextField({
					allowBlank: false
				})
			},{
				header: _t('title_alias'), 
				dataIndex: 'title_alias', 
				sortable: true,
				hidden: true,
				width: 200,
				editor: new Ext.form.TextField({
					allowBlank: false
				})
			},{
				header: _t('artist'), 
				dataIndex: 'artist', 
				sortable: true, 
				width: 250
			},{
				header: _t('account'), 
				dataIndex: 'account', 
				sortable: true, 
				width: 250
			},{
				header: _t('date_added'), 
				dataIndex: 'date_added',
				width: 130,
				sortable: true,
				hidden: true
			},{
				header: _t('date_modified'), 
				dataIndex: 'date_modified',
				width: 130,
				sortable: true
			},'status',{
				custom: 'edit',
				redirect: intelli.config.admin_url+'/manage/lyrics/edit/?id=',
				icon: 'edit-grid-ico.png',
				title: _t('edit')
			}
			,'remove'
		]
	};
}();

Ext.onReady(function()
{
	intelli.lyric.oGrid = new intelli.exGrid(intelli.lyric);
	
	intelli.lyric.oGrid.cfg.tbar = new Ext.Toolbar(
	{
		items:[
		_t('title') + ':',
		{
			xtype: 'textfield',
			name: 'searchTitle',
			id: 'searchTitle',
			width: 100,
			emptyText: _t('input_title')
		},
		'&nbsp;&nbsp;' + _t('artist') + ':',
		{
			xtype: 'textfield',
			name: 'searchArtist',
			id: 'searchArtist',
			width: 100,
			emptyText: _t('input_artist')
		},
		'&nbsp;&nbsp;' + _t('account') + ':',
		{
			xtype: 'textfield',
			name: 'searchAccount',
			id: 'searchAccount',
			width: 100,
			emptyText: _t('input_account')
		},
		'&nbsp;&nbsp;' + _t('status') + ':',
		{
			xtype: 'combo',
			typeAhead: true,
			triggerAction: 'all',
			editable: false,
			lazyRender: true,
			store: intelli.lyric.oGrid.cfg.statusesStoreWithAll,
			value: 'all',
			width: 100,
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
				var account = Ext.getCmp('searchAccount').getValue();
				var status = Ext.getCmp('stsFilter').getValue();

				if('' != title || '' != artist || '' != account || '' != status)
				{
					intelli.lyric.oGrid.dataStore.baseParams =
					{
						action: 'get',
						status: status,
						artist: artist,
						account: account,
						title: title
					};

					intelli.lyric.oGrid.dataStore.reload();
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
				Ext.getCmp('searchAccount').reset();
				Ext.getCmp('stsFilter').setValue('all');

				intelli.lyric.oGrid.dataStore.baseParams =
				{
					action: 'get',
					title: '',
					artist: '',
					account: '',
					status: ''
				};

				intelli.lyric.oGrid.dataStore.reload();
			}
		}]
	});
	intelli.lyric.oGrid.init();
	
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