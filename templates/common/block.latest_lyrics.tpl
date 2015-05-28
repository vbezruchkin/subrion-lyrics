{if !empty($latest_lyrics)}
	{foreach $latest_lyrics as $lyric}
		<div class="info"><i class="icon-calendar"></i> {$lyric.date_added|date_format:$config.date_format}</div>
		<div class="title"><a href="{ia_url item='lyrics' data=$lyric type='url'}">{$lyric.title}</a></div>
		{if !$lyric@last}<hr />{/if}
	{/foreach}
{/if}