{if !empty($new_artists)}
	<ul class="unstyled sidebar-list">
		{foreach $new_artists as $artist}
			{include file='lyrics:brief/artist.tpl'}
		{/foreach}
	</ul>
{/if}