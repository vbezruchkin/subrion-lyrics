{if $account_lyrics}
	{include file='all-items-page.tpl' all_items=$account_lyrics all_item_fields=$fields all_item_type='lyrics'}

	{navigation aTotal=$total_lyrics aTemplate=$smarty.const.IA_SELF|cat:'?page={page}' aItemsPerPage=20 aNumPageItems=5 aTruncateParam=1}
{else}
	<div class="alert alert-info">{lang key='my_lyrics_not_added'}</div>
{/if}

{if $albums}
	<h2>{lang key='my_albums'}</h2>
	{foreach from=$albums item=album}
		{$album.title} - {ia_url item='albums' data='albums'}
	{/foreach}
{/if}

{ia_hooker name='smartyMyLyricsBeforeFooter'}