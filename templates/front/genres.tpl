{if $genres}
	{include file="all-items-page.tpl" all_items=$genres all_item_fields=$fields all_item_type="genres"}
	
	{navigation aTotal=$aTotal aTemplate=$aTemplate aItemsPerPage=$aItemsPerPage aNumPageItems=5 aTruncateParam=1}
{else}
	<div class="alert alert-info">{lang key="lyrics_not_added"}</div>
{/if}