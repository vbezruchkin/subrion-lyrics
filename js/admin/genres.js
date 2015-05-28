Ext.onReady(function()
{
	intelli.genres = new IntelliGrid(
	{
		columns: [
			'selection',
			'expander',
			{name: 'title', title: _t('title'), width: 2, editor: 'text'},
			{name: 'title_alias', title: _t('title_alias'), width: 220},
			{name: 'date_modified', title: _t('date_modified'), width: 120},
			'status',
			'update',
			'delete'
		],
		expanderTemplate: '{description}',
		fields: ['description'],
		texts: {
			delete_single: _t('are_you_sure_to_delete_selected_genre'),
			delete_multiple: _t('are_you_sure_to_delete_selected_genres')
		},
		url: intelli.config.admin_url + '/lyrics/genres/'
	}, false);

	intelli.genres.toolbar = Ext.create('Ext.Toolbar', {items:
	[
		{
			emptyText: _t('title'),
			xtype: 'textfield',
			id: 'searchTitle',
			listeners: intelli.gridHelper.listener.specialKey
		}, {
			emptyText: _t('status'),
			xtype: 'combo',
			typeAhead: true,
			editable: false,
			id: 'stsFilter',
			lazyRender: true,
			store: intelli.genres.stores.statuses,
			displayField: 'title',
			valueField: 'value'
		}, {
			text: '<i class="i-search"></i> ' + _t('search'),
			id: 'fltBtn',
			handler: function()
			{
				var text = Ext.getCmp('searchTitle').getValue();
				var status = Ext.getCmp('stsFilter').getValue();

				if (text || status)
				{
					intelli.genres.store.getProxy().extraParams = {text: text, status: status};
					intelli.genres.store.reload();
				}
			}
		}, '-', {
			text: '<i class="i-close"></i> ' + _t('reset'),
			id: 'resetBtn',
			handler: function()
			{
				Ext.getCmp('searchTitle').reset();
				Ext.getCmp('stsFilter').reset();

				intelli.genres.store.getProxy().extraParams = {};
				intelli.genres.store.reload();
			}
		}
	]});

	intelli.genres.init();

	if (intelli.urlVal('status'))
	{
		Ext.getCmp('stsFilter').setValue(intelli.urlVal('status'));
	}

	var search = intelli.urlVal('quick_search');
	if (null != search)
	{
		Ext.getCmp('searchTitle').setValue(search);
	}
});