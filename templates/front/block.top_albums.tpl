{if !empty($top_albums)}
	<ul class="unstyled sidebar-list">
		{foreach $top_albums as $album}
			{include file='lyrics:brief/album.tpl'}
		{/foreach}
	</ul>
{/if}