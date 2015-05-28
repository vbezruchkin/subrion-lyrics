<div class="info">
	<!-- AddThis Button BEGIN -->
	<div class="addthis_toolbox addthis_default_style" style="float: left;">
		<a href="http://www.addthis.com/bookmark.php?v=250&amp;username=xa-4c6e050a3d706b83" class="addthis_button_compact">Share</a>
	</div>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4c6e050a3d706b83"></script>
	<!-- AddThis Button END -->

	<img src="{$smarty.const.IA_TPL_URL}img/calendar.png" alt="" />
	{$album.date_added|date_format:$config.date_format}
	
	{if $album.views_num > 0}&nbsp;|&nbsp; <img src="{$smarty.const.IA_TPL_URL}img/chart.png" alt="" /> {$album.views_num} {lang key='views'}{/if}

	{if $smarty.const.IN_USER && $smarty.session.user.id != $album.member_id}
		&nbsp;|&nbsp;{printFavorites item=$album itemtype=albums}		
	{/if}
</div>

<h2>{$artist.title} {lang key='lyrics'}</h2>
{if $lyrics}
	{include file='all-items-page.tpl' all_items=$lyrics all_item_fields=$fields all_item_type='lyrics'}
{else}
	<div class="alert alert-info">{lang key='album_lyrics_not_added'}</div>
{/if}

<h2>{$artist.title} {lang key='other_albums'}</h2>
{if $albums}
	{foreach from=$albums item=album}
		<p>{$album.title} - {ia_url item='albums' data=$album}</p>
	{/foreach}
{else}
	<div class="alert alert-info">{lang key='more_artist_albums_not_added'}</div>
{/if}

{ia_hooker name='smartyViewAlbumBeforeFooter'}