{if !empty($popular_lyrics)}
	{foreach $popular_lyrics as $lyric}
		<div class="info"><i class="icon-eye-open"></i> {$lyric.views_num} {lang key='views'}</div>
		<div class="title"><a href="{ia_url item='lyrics' data=$lyric type='url'}">{$lyric.artist_title} - {$lyric.title}</a></div>
		{if !$lyric@last}<hr />{/if}
	{/foreach}
{/if}