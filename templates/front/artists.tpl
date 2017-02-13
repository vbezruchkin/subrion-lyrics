{if $search_alphas}
	<div class="accounts-sorting block movable">
		{foreach $search_alphas as $onealpha}
			{if $onealpha eq $alpha}
				<span class="btn btn-mini disabled">{$onealpha}</span>
			{else}
				<a href="{$smarty.const.IA_URL}artists/{$onealpha}/" class="btn btn-mini">{$onealpha}</a>
			{/if}
		{/foreach}
	</div>
{/if}

{if $artists}
	{include file='all-items-page.tpl' all_items=$artists all_item_fields=$fields all_item_type='artists'}
	
	{navigation aTotal=$aTotal aTemplate=$aTemplate aItemsPerPage=$aItemsPerPage aNumPageItems=5 aTruncateParam=1}
{else}
	<div class="alert alert-info">{lang key='no_artists_added'}</div>
{/if}