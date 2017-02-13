{if !empty($genres_list)}
	<ul class="unstyled sidebar-list">
		{foreach $genres_list as $onegenre}
			<li><a href="{ia_url item='genres' data=$onegenre type='url'}">{$onegenre.title}</a></li>
		{/foreach}
	</ul>
{/if}