{if isset($author) && $author}
	<div class="items-block company-owner">
		<div class="item author">
			<a href="" class="thumbnail">{printImage imgfile=$author.avatar width=$config.thumb_w height=$config.thumb_h}</a>
			<p class="title"><i class="icon-user"></i> <a href="{ia_url data=$author item='accounts' type='url'}">{$author.fullname}</a></p>
		</div>
	</div>
{/if}