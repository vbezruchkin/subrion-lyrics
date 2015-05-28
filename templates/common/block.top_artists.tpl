{if !empty($top_artists)}
	<ul class="unstyled sidebar-list">
		{foreach $top_artists as $artist}
			{include file='lyrics:brief/artist.tpl'}
		{/foreach}
	</ul>
{/if}