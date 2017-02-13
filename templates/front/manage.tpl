{capture name='lyric_general' append='fieldset_before'}

	{include file='plans.tpl' item=$item}

	<div class="control-group">
		<label class="control-label" for="artist">{lang key='artist'}:</label>
		<div class="controls">
			<input class="common" type="text" name="artist" id="artist" value="{if isset($item.artist)}{$item.artist}{/if}" size="50" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="album">{lang key='album'}:</label>
		<div class="controls">
			<select name="album" id="album">
				{if !empty($albums)}
					{foreach from=$albums item=album}
						<option value="{$album.id}"{if $album.id == $item.id_album}selected="selected" {/if}>{$album.title}</option>
					{/foreach}
				{else}
					<option value=""> - please select artist first - </option>
				{/if}
			</select>
		</div>
	</div>
{/capture}

<form action="{$smarty.const.IA_SELF}{if isset($smarty.get.id)}?edit={$smarty.get.id}{/if}" method="post" enctype="multipart/form-data" id="lyric_form" class="form-subrion">

	{preventCsrf}

	{include file="field-type-content-fieldset.tpl" item_sections=$sections item=$item}

	{include file="captcha.tpl"}
	
	<div class="form-actions">
		<input type="submit" class="btn btn-success" name="change_info" value="{lang key='save_changes'}" />
	</div>
</form>

{ia_print_js files="jquery/plugins/jquery.form,jquery/plugins/jquery.block,jquery/plugins/jquery.textcounter,jquery/plugins/autocomplete/jquery.autocomplete,_IA_URL_modules/lyrics/js/front/submit"}
{ia_print_css files="_IA_URL_js/jquery/plugins/autocomplete/jquery.autocomplete"}