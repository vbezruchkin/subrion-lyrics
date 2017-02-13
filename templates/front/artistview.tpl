{if $albums}
	<h2>{lang key='albums'}: {$albums|count}</h2>
	{foreach $albums as $album}
		<p>{$album.title} - {ia_url item='albums' data=$album}</p>
	{/foreach}
{/if}

{if $lyrics}
	<h2>{lang key='lyrics'}: {$total_lyrics}</h2>
	
	{include file='all-items-page.tpl' all_items=$lyrics all_item_fields=$fields all_item_type='lyrics'}
	
	{navigation aTotal=$total_lyrics aTemplate=$smarty.const.IA_SELF|cat:'?page={page}' aItemsPerPage=20 aNumPageItems=5 aTruncateParam=1}
{/if}

{ia_hooker name='smartyViewArtistBeforeFooter'}