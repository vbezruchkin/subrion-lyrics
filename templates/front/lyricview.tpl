<div class="lyric_info">
	<div class="pull-left" style="position: absolute;">
		<script type="text/javascript" src="http://cd02.static.jango.com/javascripts/promos/jangolib_3.js"></script>
		<link href="http://cd02.static.jango.com/stylesheets/promo/jangoscroller_3b.css" type="text/css">

		<div class="scroller">
			<div class="playerBG">
				<a href="http://www.jango.com/play/{$artist.title|escape:'url'}/{$lyric.title|escape:'url'}" target="_blank"><img src="http://cd02.static.jango.com/images/promo/playerLyrics160Black.gif" border="0" alt="free music" /></a>
			</div>
			<div style="position: absolute; top: 20px; left:12px;">
				<marquee width="140" scrolldelay="150">
					<a href="http://www.jango.com/play/{$artist.title}/{$lyric.title}" class="playList">{$artist.title} - {$lyric.title}</a>
				</marquee>
			</div>
		</div>
	</div>

	<div class="pull-left" style="margin-left: 170px; position: relative; margin-bottom: 20px;">
		<p>{lang key='artist'}: <a href="{ia_url item='artists' data=$artist type='url'}" class="b">{$artist.title}</a></p>
		<p>{lang key='album'}: <a href="{ia_url item='albums' data=$album type='url'}" class="b">{$album.title}</a>, {if $album.year}<span class="b">{$album.year}</span>{/if}</p>
	</div>
	<div style="clear: both">&nbsp;</div>
</div>

<p class="c b"><a href="http://www.ringtonematcher.com/co/ringtonematcher/02/noc.asp?sid=lyrescript&amp;artist={$artist.title|escape:'url'}&amp;song={$lyric.title|escape:'url'}" class="Ringtones">Send {$artist.title} - "{$lyric.title}" Ringtone to your Cell</a></p>

<div class="lyrics-body" style="margin: 20px 0;">{$lyric.body|nl2br}</div>

<div class="info">
	<!-- AddThis Button BEGIN -->
	<div class="addthis_toolbox addthis_default_style" style="float: left;">
		<a href="http://www.addthis.com/bookmark.php?v=250&amp;username=xa-4c6e050a3d706b83" class="addthis_button_compact">Share</a>
	</div>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4c6e050a3d706b83"></script>
	<!-- AddThis Button END -->

	&nbsp;|&nbsp;<img src="{$smarty.const.IA_TPL_URL}img/calendar.png" alt="" />
	{$lyric.date_added|date_format:$config.date_format}
	
	{if $lyric.views_num > 0}&nbsp;|&nbsp; <img src="{$smarty.const.IA_TPL_URL}img/chart.png" alt="" /> {$lyric.views_num} {lang key='views'}{/if}

	{if $smarty.const.IN_USER AND $smarty.session.user.id eq $lyric.account_id}
		&nbsp;|&nbsp;<img src="{$smarty.const.IA_TPL_URL}img/edit_16.png" alt="{lang key="edit"}" />
		<a href="{ia_url action='edit' item='lyrics' data=$lyric type='url'}" rel="nofollow">{lang key='edit'}</a>
	{/if}

	{if $smarty.const.IN_USER && $smarty.session.user.id != $lyric.account_id}
		&nbsp;|&nbsp;{printFavorites item=$lyric itemtype=lyrics}		
	{/if}
</div>

<div class="c b">
	<!-- RINGTONE 1 -->
	<a href="http://www.ringtonematcher.com/co/ringtonematcher/02/noc.asp?sid=lyrescript&amp;artist={$artist.title|escape:'url'}&amp;song={$lyric.title|escape:'url'}" class="Ringtones">Send {$artist.title} - "{$lyric.title}" Ringtone to your Cell</a>
	<!-- END OF RINGTONE 1 -->
</div>

{ia_hooker name='smartyViewLyricBeforeFooter'}