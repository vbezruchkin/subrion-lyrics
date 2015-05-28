{if !empty($new_albums)}
	<ul class="unstyled sidebar-list">
		{foreach $new_albums as $album}
			{include file='lyrics:brief/album.tpl'}
		{/foreach}
	</ul>
{/if}