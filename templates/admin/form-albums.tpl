<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">

	{preventCsrf}

	{capture name='album_general' append='fieldset_before'}
		<tr>
			<td class="t1">{lang key="artist"}:</td>
			<td><input class="common" type="text" name="artist" id="artist" value="{if isset($item.artist_title)}{$item.artist_title}{/if}" size="45"/></td>
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
	var itemname = 'albums';
{/ia_add_js}

{ia_print_js files="_IA_URL_modules/lyrics/js/admin/footer"}