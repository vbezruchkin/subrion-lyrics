{if !empty($new_lyrics)}
	<ul class="unstyled sidebar-list">
		{foreach $new_lyrics as $lyric}
			{include file='lyrics:brief/lyric.tpl'}
		{/foreach}
	</ul>
{else}
	<div class="alert alert-info">{lang key='no_lyrics'}</div>
{/if}