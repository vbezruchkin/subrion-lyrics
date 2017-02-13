<h2>{lang key='artists'}</h2>
{if $artists}
	{include file='all-items-page.tpl' all_items=$artists all_item_fields=$fields all_item_type='artists'}
{else}
	<div class="alert alert-info">{lang key='genre_artists_not_added'}</div>
{/if}