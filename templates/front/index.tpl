{if $new_lyrics}
	{include file='all-items-page.tpl' all_items=$new_lyrics all_item_fields=$fields all_item_type='lyrics'}
{else}
	<div class="alert alert-info">{lang key='lyrics_not_added'}</div>
{/if}