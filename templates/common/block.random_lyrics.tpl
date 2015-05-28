{if !empty($random_lyrics)}
	{foreach $random_lyrics as $lyric}
		<div class="info"><i class="icon-user"></i> <a href="{ia_url item='artists' data=$lyric type='url' action='artist_info'}">{$lyric.artist_title}</a></div>
		<div class="title"><a href="{ia_url item='lyrics' data=$lyric type='url'}">{$lyric.title}</a></div>
		{if !$lyric@last}<hr />{/if}
	{/foreach}
{/if}