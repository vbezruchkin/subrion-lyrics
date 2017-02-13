<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">

	{preventCsrf}

	{capture name='artist_general' append='fieldset_before'}
		<tr>
			<td class="t1">{lang key="genres"}:</td>
			<td>
			{if !empty($genres)}
				{foreach from=$genres item=genre}
					<div style="float: left; width: 150px; "><input type="checkbox" name="genres[]" id="genres[{$genre.id}]" value="{$genre.id}" {if in_array($genre.id, $item.genres)}checked="checked" {/if}/> <label for="genres[{$genre.id}]" style="white-space: nowrap;">{$genre.title}</label></div>
				{/foreach}
			{else}
				{lang key="genres_not_added"}
			{/if}
			</td>
		</tr>
		<tr id="tr_move_categ" style="display: none;">
			<td class="t1">{lang key="title_alias"}:</td>
			<td><input class="common" type="text" name="title_alias" id="title_alias" value="{if isset($item.title_alias)}{$item.title_alias}{/if}" size="45"/>
				<div style="margin-left: 3px; padding: 4px;" id="title_box">
					<span>{lang key="page_url_will_be"}:&nbsp;<span>
					<span id="title_url" style="padding: 3px; margin: 0; background: #FFE269;">{$smarty.const.IA_URL}</span>
				</div>
			</td>
		</tr>
	{/capture}

	{include file='field-type-content-fieldset.tpl' item_sections=$fields_groups isSystem=true}
</form>

{ia_add_js}
	var itemname = 'artists';
{/ia_add_js}

{ia_print_js files="_IA_URL_modules/lyrics/js/admin/footer"}